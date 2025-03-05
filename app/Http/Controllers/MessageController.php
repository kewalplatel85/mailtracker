<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class MessageController extends Controller
{
    //
    public function showMessages(){
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

        $messages = $twilio->messages->read(['to' => env('TWILIO_PHONE_NUMBER')], 20);

        return ['messages' => $messages];
    }


    public function sendReply(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string',
        ]);

        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $twilio->messages->create($request->to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Reply sent!');
    }
}
