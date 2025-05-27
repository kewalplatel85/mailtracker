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
        $packageLogs = Package::select(
            'mailbox_number',
            'customer_name',
            'phone_number',
            'status',
            'created_at',
            'tracking_number', // Added this
            'id' // Added this
        )
        ->where('status', 'Incoming')
        ->get()
        ->groupBy('mailbox_number')
        ->map(function ($group) {
            return (object) [
                'mailbox_number' => $group->first()->mailbox_number,
                'customer_name' => $group->first()->customer_name,
                'phone_number' => $group->first()->phone_number,
                'status' => $group->first()->status,
                'date_received' => \Carbon\Carbon::parse($group->first()->created_at)->format('d-m-Y'), // Fix here
                'package_count' => $group->count(),
                'tracking_numbers' => $group->pluck('tracking_number')->toArray(),
                'id' => $group->pluck('id')->toArray(),
            ];
        });

        $messagesController = new MessageController();
        $inboxData = $messagesController->index();

        return view('packagelogs', [
            'packages' => $packageLogs,
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
                    'id' => $group->pluck('id')->toArray(),
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
            'tracking_numbers' => 'required|array',
            'package_status' => 'required|string',
            'customer_name' => 'required|string',
            'sms' => 'required|string',
        ]);

        $customerPhone = $request->customer_phone;

        if ($customerPhone) { // Only validate and clean if phone exists
            $request->validate([
                'customer_phone' => 'required|string|min:10|max:15',
            ]);
            $customerPhone = preg_replace('/\D/', '', $customerPhone); // Remove non-numeric characters
        } else {
            $customerPhone = null;
        }

        $trackingNumbers = $request->tracking_numbers;

        Package::whereIn('tracking_number', $trackingNumbers)
            ->update(['status' => $request->package_status]);

        $trackingList = implode(", ", $trackingNumbers);

        // FIX: Check if customerPhone is NOT null before sending SMS
        if ($customerPhone) {
            try {
                $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
                $twilio->messages->create($customerPhone, [ // Ensure this is not null
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => "Hi {$request->customer_name}, {$request->sms} Tracking Number: {$trackingList}."
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error sending SMS: ' . $e->getMessage()]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Package successfully picked, SMS sent!']);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'packages' => 'required|array',
            'packages.*.id' => 'required|integer|exists:packages,id',
            'packages.*.tracking_number' => 'required|string',
            'status' => 'required|string|in:Outgoing',
            'sms' => 'required|string'
        ]);

        $firstPackage = null;
        $phone = '';
        $customer = '';
        $trackingNumbers = [];

        foreach ($request->packages as $packageData) {
            $package = Package::find($packageData['id']);

            if ($package) {
                $package->status = $request->status;
                $package->save();

                // Set only once for SMS
                if (!$firstPackage) {
                    $firstPackage = $package;
                    $phone = preg_replace('/\D/', '', $package->phone_number);
                    $customer = $package->customer_name;
                }

                $trackingNumbers[] = $packageData['tracking_number'];
            }
        }

        // Prepare message
        if ($firstPackage && $phone) {
            try {
                $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
                $messageBody = '';

                if (count($trackingNumbers) === 1) {
                    $messageBody = "Hi {$customer}, {$request->sms} Tracking Number: {$trackingNumbers[0]}.";
                } else {
                    $messageBody = "Hi {$customer}, {$request->sms} Total Packages Claimed: " . count($trackingNumbers) . ".";
                }

                $twilio->messages->create($phone, [
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => $messageBody
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error sending SMS: ' . $e->getMessage()]);
            }
        }

        return response()->json(['message' => 'All packages updated and SMS sent!']);
    }

    public function deletePackage(Request $request)
    {
        $packageId = $request->input('package_id'); // Individual delete
        $status = $request->input('status');

        if ($status !== "Outgoing") {
            return response()->json(['success' => false, 'message' => 'Only "Outgoing" packages can be deleted.'], 400);
        }

        if ($packageId) {
            // Delete a single package by ID
            $deleted = Package::where('id', $packageId)->where('status', 'Outgoing')->delete();

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Package deleted successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Package not found or already deleted.'], 404);
            }
        } else {
            // Bulk delete - Delete all "Outgoing" packages
            $deletedCount = Package::where('status', 'Outgoing')->delete();

            return response()->json([
                'success' => true,
                'message' => $deletedCount > 0
                    ? "Deleted all outgoing packages successfully."
                    : "No outgoing packages found to delete."
            ]);
        }
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
