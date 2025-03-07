<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\Package;

class PackageController extends Controller
{
    //
    public function index(){
        $status = 'Incoming';
        $package = $this->getLogs($status);

        // Instantiate MessageController and fetch SMS messages
        $messagesController = new MessageController();
        $inboxData = $messagesController->index();

        $receivedMessages = $inboxData['receivedMessages'];
        $sentMessages = $inboxData['sentMessages'];

        return view('packagelogs', [
            'packageLogs'=>$package,
            'receivedMessages' => $receivedMessages,
            'sentMessages' => $sentMessages
        ]);
    }

    public function getLogs($status){
        $packages = Package::where('status',$status)->get();

        return $packages;
    }

    public function getPackages(Request $request){
        $status = $request->query('status');
        $packages = Package::where('status', $status)->get()->groupBy('mailbox_number');

        $formattedPackages = $packages->map(function ($group) {
            return [
                'mailbox_number' => $group->first()->mailbox_number,
                'customer_name' => $group->first()->customer_name,
                'phone_number' => $group->first()->phone_number,
                'package_count' => $group->count(),
                'tracking_numbers' => $group->pluck('tracking_number')->toArray(),
                'status' => $group->first()->status,
                'date_received' => $group->first()->created_at->format('Y-m-d'),
            ];
        });

        return response()->json($formattedPackages->values());
    }

    public function checkTrackingNumberExist(Request $request){
        $request->validate([
            'tracking_number' => 'required|string|max:255',
        ]);

        // Find the tracking number in the database
        $tracking = Package::where('tracking_number', $request->tracking_number)->first();
        // If not found, return a "not exists" response
        return response()->json([
            'exists' => (bool) $tracking,
            'status' => $tracking ? $tracking->status : null,
            'customer_name' => $tracking ? $tracking->customer_name : null,
            'mailbox_number' => $tracking ? $tracking->mailbox_number : null,
        ]);
    }

    public function outgoingPackage(Request $request){
        $customerPhone = preg_replace('/\D/', '', $request->customer_phone);
        $trackingNumbers = $request->tracking_numbers;

        foreach ($request->tracking_numbers as $tracking_number) {
            Package::where('tracking_number', $tracking_number)
                ->update(['status' => $request->package_status]);
        }

        $trackingList = implode(", ", $trackingNumbers);
            // Send SMS using Twilio
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            $twilio->messages->create($customerPhone, [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => "Hi {$request->customer_name}, {$request->sms} Tracking Number: {$trackingList}."
            ]);

        return response()->json(['success' => true, 'message' => 'Package Succesfully Picked, SMS sent!']);
    }
}
