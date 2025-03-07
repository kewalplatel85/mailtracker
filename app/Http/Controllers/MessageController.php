<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class MessageController extends Controller
{
    // Display SMS inbox with received and sent messages
    public function index()
    {
        $receivedMessages = $this->fetchMessagesFromTwilio('inbound');
        $sentMessages = $this->fetchMessagesFromTwilio('outbound-api');

        return view('sms.inbox', compact('receivedMessages', 'sentMessages'));
    }


    // Send a custom message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'message' => 'required',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->phone);

        // Add +1 if the phone number is 10 digits (assumes US/Canada)
        if (strlen($phone) == 10) {
            $phone = "+1$phone";
        } elseif (!str_starts_with($phone, '+')) {
            $phone = "+$phone";
        }


        try {
            $this->sendTwilioMessage($phone, $request->message);

            return response()->json([
                'success' => 'Message sent successfully!',
                'redirect' => route('dashboard') // Add redirect URL
            ]);
        } catch (TwilioException $e) {
            return response()->json(['error' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }

    // Handle reply to a message
    public function sendReply(Request $request)
    {
        $request->validate([
            'to' => 'required',
            'message' => 'required',
        ]);

        try {
            $this->sendTwilioMessage($request->to, $request->message);

            return response()->json(['success' => 'Reply sent successfully!']);
        } catch (TwilioException $e) {
            return response()->json(['error' => 'Failed to send reply: ' . $e->getMessage()], 500);
        }
    }

    // Fetch messages from Twilio (inbound or outbound)
    private function fetchMessagesFromTwilio($direction)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);

        try {
            $messages = $twilio->messages->read(['direction' => $direction], 10);

            return collect($messages);
        } catch (TwilioException $e) {

            return collect([]);
        }
    }

    // Send a message via Twilio
    private function sendTwilioMessage($to, $body)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);

        return $twilio->messages->create($to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => $body,
        ]);
    }

    public function sendTextBlast(Request $request)
    {
        $request->validate([
            'phone_numbers' => 'required|string',
            'blast_message' => 'required|string|max:1600',
        ]);

        // Clean and filter phone numbers
        $phoneNumbers = array_filter(array_map('trim', explode(',', $request->phone_numbers)));
        $message = $request->blast_message;

        if (empty($phoneNumbers)) {
            return redirect()->back()->withErrors(['phone_numbers' => 'Please provide valid phone numbers.']);
        }

        // Twilio credentials
        $twilioSid = config('services.twilio.sid');
        $twilioToken = config('services.twilio.token');
        $twilioFrom = config('services.twilio.from');

        // Ensure Twilio credentials are available
        if (!$twilioSid || !$twilioToken || !$twilioFrom) {
            return redirect()->back()->withErrors(['error' => 'Twilio configuration is missing.']);
        }

        $twilio = new Client($twilioSid, $twilioToken);

        $failedNumbers = [];

        foreach ($phoneNumbers as $number) {
            try {
                // Send SMS via Twilio
                $twilio->messages->create($number, [
                    'from' => $twilioFrom,
                    'body' => $message,
                ]);
            } catch (TwilioException $e) {
                $failedNumbers[] = $number;
            }
        }

        // Prepare feedback message
        if (count($failedNumbers) > 0) {
            $failedList = implode(', ', $failedNumbers);
            return redirect()->back()->with('warning', "Text blast sent, but failed for: $failedList");
        }

        return redirect()->back()->with('success', 'Text blast sent successfully!');
    }

}
