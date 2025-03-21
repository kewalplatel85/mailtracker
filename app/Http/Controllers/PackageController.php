<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Twilio\Rest\Client;
use App\Models\Package;

class PackageController extends Controller
{
    //
    public function index()
    {
        $packageLogs = Package::where('status', 'Incoming')->get();

        $messagesController = new MessageController();
        $inboxData = $messagesController->index();

        return view('packagelogs', [
            'packageLogs' => $packageLogs,
            'receivedMessages' => $inboxData['receivedMessages'],
            'sentMessages' => $inboxData['sentMessages'],
        ]);
    }

    public function getPackages(Request $request)
    {
        $status = $request->query('status', 'Incoming');
        $packages = Package::where('status', $status)
            ->get()
            ->groupBy('mailbox_number')
            ->map(function ($group) {
                return [
                    'id' => $group->first()->id,
                    'mailbox_number' => $group->first()->mailbox_number,
                    'customer_name' => $group->first()->customer_name,
                    'phone_number' => $group->first()->phone_number,
                    'package_count' => $group->count(),
                    'tracking_numbers' => $group->pluck('tracking_number')->toArray(),
                    'status' => $group->first()->status,
                    'date_received' => $group->first()->created_at->format('Y-m-d'),
                ];
            })
            ->values();

        return response()->json($packages);
    }

    public function checkTrackingNumberExist(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:255',
        ]);

        $tracking = Package::where('tracking_number', $request->tracking_number)->first();

        return response()->json([
            'exists' => (bool) $tracking,
            'status' => optional($tracking)->status,
            'customer_name' => optional($tracking)->customer_name,
            'mailbox_number' => optional($tracking)->mailbox_number,
        ]);
    }

    public function outgoingPackage(Request $request)
    {
        $request->validate([
            'customer_phone' => 'required|string',
            'tracking_numbers' => 'required|array',
            'package_status' => 'required|string',
            'customer_name' => 'required|string',
            'sms' => 'required|string',
        ]);

        $customerPhone = preg_replace('/\D/', '', $request->customer_phone);
        $trackingNumbers = $request->tracking_numbers;

        Package::whereIn('tracking_number', $trackingNumbers)
            ->update(['status' => $request->package_status]);

        $trackingList = implode(", ", $trackingNumbers);

        try {
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            $twilio->messages->create($customerPhone, [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => "Hi {$request->customer_name}, {$request->sms} Tracking Number: {$trackingList}."
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error sending SMS: ' . $e->getMessage()]);
        }

        return response()->json(['success' => true, 'message' => 'Package successfully picked, SMS sent!']);
    }

    public function deletePackage(Request $request) {
        $mailboxNumber = $request->input('mailbox_number');
        $status = $request->input('status');

        if ($status !== "Outgoing") {
            return response()->json(['success' => false, 'message' => 'Only "Outgoing" packages can be deleted.'], 400);
        }

        Package::where('mailbox_number', $mailboxNumber)->where('status', 'Outgoing')->delete();

        return response()->json(['success' => true, 'message' => 'Deleted all "Outgoing" tracking numbers for mailbox #' . $mailboxNumber]);
    }


    public function deleteAllOutgoing(Request $request)
    {
        try {
            // Delete all outgoing packages
            $deleted = Package::where('status', 'Outgoing')->delete();

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'All outgoing packages deleted successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'No outgoing packages found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting outgoing packages.']);
        }
    }

    public function getLastPackageID(): JsonResponse
    {
        $lastPackage = Package::latest('id')->first();
        return response()->json(['last_id' => $lastPackage ? $lastPackage->id : 0]);
    }

}
