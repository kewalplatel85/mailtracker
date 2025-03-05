<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class MessageController extends Controller
{
    //
    // public function showMessages(){
    //     $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

    //     $messages = $twilio->messages->read(['to' => env('TWILIO_PHONE_NUMBER')], 20);

    //     return ['messages' => $messages];
    // }


    // public function sendReply(Request $request)
    // {
    //     $request->validate([
    //         'to' => 'required|string',
    //         'message' => 'required|string',
    //     ]);

    //     $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    //     $twilio->messages->create($request->to, [
    //         'from' => env('TWILIO_PHONE_NUMBER'),
    //         'body' => $request->message,
    //     ]);

    //     return redirect()->back()->with('success', 'Reply sent!');
    // }

    // Display the SMS inbox and custom message form
    public function index()
    {
        // Fetch messages from Twilio (example)
        $messages = $this->fetchMessagesFromTwilio();

        return ['messages' => $messages];
    }

    // Send a custom message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'message' => 'required',
        ]);

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);

        try {
            $message = $twilio->messages
                ->create($request->phone, // to
                    [
                        "body" => $request->message,
                        "from" => env('TWILIO_PHONE_NUMBER')
                    ]
                );

            return back()->with('success', 'Message sent successfully!');
        } catch (TwilioException $e) {
            return back()->with('error', 'Failed to send message: ' . $e->getMessage());
        }
    }

    // Handle reply to a message
    public function sendReply(Request $request)
    {
        $request->validate([
            'to' => 'required',
            'message' => 'required',
        ]);

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);

        try {
            $message = $twilio->messages
                ->create($request->to, // to
                    [
                        "body" => $request->message,
                        "from" => env('TWILIO_PHONE_NUMBER')
                    ]
                );

            return back()->with('success', 'Reply sent successfully!');
        } catch (TwilioException $e) {
            return back()->with('error', 'Failed to send reply: ' . $e->getMessage());
        }
    }

    // Fetch messages from Twilio (example implementation)
    private function fetchMessagesFromTwilio()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);

        try {
            // Fetch the latest 10 messages
            $messages = $twilio->messages
                ->read([], 10); // Adjust limit as needed

            return $messages;
        } catch (TwilioException $e) {
            // Handle error (e.g., log it)
            return [];
        }
    }
}
