<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use App\Models\Package;
use App\Http\Controllers\MessageController;

class DashboardController extends Controller
{
    //
    public function index(){
        $filePath = 'uploads/latest_file.csv';
        $data = [];

        // Check if the file exists and load its contents
        if (Storage::exists($filePath)) {
            $data = $this->parseFile(Storage::path($filePath));
        }

        // Instantiate MessageController and fetch SMS messages
        $messagesController = new MessageController();
        $inboxData = $messagesController->index();

        $receivedMessages = $inboxData['receivedMessages'];
        $sentMessages = $inboxData['sentMessages'];

        return view('dashboard', [
            'data' => $data,
            'receivedMessages' => $receivedMessages,
            'sentMessages' => $sentMessages
        ]);
    }

    private function parseFile($filePath){
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    public function savePackage(Request $request) {
        $request->validate([
            'tracking_numbers' => 'required|array',
            'tracking_numbers.*' => 'required|string',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string|min:10|max:15',
            'package_status' => 'required|string',
        ]);

        $trackingNumbers = $request->tracking_numbers;
        $customerPhone = preg_replace('/\D/', '', $request->customer_phone); // Remove non-numeric characters

        // ✅ Check if it's a new client
        $mailbox = $request->mailbox;
        if (!$mailbox) {
            $mailbox = null; // Allow null for new clients
        } else {
            $request->validate([
                'mailbox' => 'required|integer',
            ]);
        }

        foreach ($trackingNumbers as $tracking_number) {
            Package::create([
                'customer_name' => $request->customer_name,
                'phone_number' => $customerPhone,
                'mailbox_number' => $mailbox,
                'tracking_number' => $tracking_number,
                'status' => $request->package_status,
            ]);
        }
        // Send Email
            // Mail::to($customer->email)->send(new \App\Mail\PackageNotification($package));
            $trackingList = implode(", ", $trackingNumbers);
            // Send SMS using Twilio
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            $twilio->messages->create($customerPhone, [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => "Hi {$request->customer_name},{$request->sms} Tracking Number: {$trackingList}."
            ]);


        return response()->json(['message' => 'Package saved and notifications sent successfully.']);
    }

}
