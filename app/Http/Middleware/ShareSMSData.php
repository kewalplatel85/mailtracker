<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\MessageController;

class ShareSMSData
{
    public function handle(Request $request, Closure $next)
    {
        // Only share SMS data for authenticated users on pages that use layout.app
        if (Auth::check()) {
            try {
                $messageController = new MessageController();
                $receivedMessages = $messageController->getReceivedMessages();
                $sentMessages = $messageController->getSentMessages();

                View::share('receivedMessages', $receivedMessages);
                View::share('sentMessages', $sentMessages);
            } catch (\Exception $e) {
                // If Twilio fails, provide empty collections
                View::share('receivedMessages', collect([]));
                View::share('sentMessages', collect([]));
            }
        }

        return $next($request);
    }
}
