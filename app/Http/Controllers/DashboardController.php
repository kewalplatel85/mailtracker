<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
        try {
            $request->validate([
                'mailbox_number' => 'nullable|string',
                'customer_name' => 'required|string',
                'package_count' => 'required|integer|min:1',
                'status' => 'required|string',
                'tracking_number' => 'nullable|string',
                'sms_message' => 'nullable|string',
                'package_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:10120',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        $imagePaths = [];
        $publicUrls = [];

        if (!Storage::disk('public')->exists('attachments')) {
            Storage::disk('public')->makeDirectory('attachments');
        }

        // 📁 Handle images from file input
        if ($request->hasFile('package_images')) {
            foreach ($request->file('package_images') as $image) {
                $filename = uniqid('upload_') . '.' . $image->getClientOriginalExtension();
                $storedPath = $image->storeAs('attachments', $filename, 'public');
                $imagePaths[] = storage_path("app/public/{$storedPath}");
                $publicUrls[] = Storage::url($storedPath);
            }
        }

        // Process tracking numbers (split by lines if multiple)
        $trackingNumbers = [];
        if ($request->tracking_number) {
            $trackingNumbers = array_filter(array_map('trim', explode("\n", $request->tracking_number)));
        }

        // If no tracking numbers provided, create empty array for the package count
        if (empty($trackingNumbers)) {
            $trackingNumbers = array_fill(0, $request->package_count, '');
        }

        // Ensure we have the right number of tracking numbers for package count
        $packageCount = max($request->package_count, count($trackingNumbers));

        // ✅ Handle mailbox number
        $mailbox = $request->mailbox_number;
        if (!$mailbox || trim($mailbox) === '') {
            $mailbox = null; // Allow null for new clients
        }

        // Extract customer phone from CSV data if mailbox is provided
        $customerPhone = null;
        if ($mailbox) {
            // Look up customer info from CSV data
            $filePath = 'uploads/latest_file.csv';
            if (Storage::exists($filePath)) {
                $data = $this->parseFile(Storage::path($filePath));
                // Search for mailbox in data to get phone number
                foreach ($data as $row) {
                    if (isset($row[0]) && trim($row[0]) == trim($mailbox)) {
                        if (isset($row[4]) && !empty(trim($row[4]))) {
                            $customerPhone = preg_replace('/\D/', '', trim($row[4]));
                        }
                        break;
                    }
                }
            }
        }

        // Create packages based on package count
        for ($i = 0; $i < $packageCount; $i++) {
            $trackingNumber = isset($trackingNumbers[$i]) ? $trackingNumbers[$i] : '';

            Package::create([
                'customer_name' => $request->customer_name,
                'phone_number' => $customerPhone,
                'mailbox_number' => $mailbox,
                'tracking_number' => $trackingNumber,
                'status' => $request->status,
            ]);
        }

        // TODO: Send SMS if customer phone is provided and SMS message is set
        // SMS functionality temporarily disabled
        /*
        if ($customerPhone && $request->sms_message) {
            $trackingList = implode(", ", array_filter($trackingNumbers));
            $smsBody = $request->sms_message;

            // Add tracking numbers to SMS if available
            if (!empty($trackingList)) {
                $smsBody .= " Tracking Number(s): {$trackingList}.";
            }

            try {
                // Send SMS using Twilio
                $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
                $twilio->messages->create($customerPhone, [
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => $smsBody
                ]);
            } catch (\Exception $e) {
                // Log SMS error but don't fail the package creation
                \Log::error('SMS sending failed: ' . $e->getMessage());
            }
        }
        */

        return response()->json([
            'message' => 'Package(s) saved successfully.',
            'packages_created' => $packageCount,
            'phone_found' => $customerPhone ? true : false,
            'sms_sent' => false // SMS temporarily disabled
        ]);
    }

}
