<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\QuickMessage;
use App\Mail\RenewalReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    /**
     * Send quick message via SMS and/or Email
     */
    public function sendQuickMessage(Request $request)
    {
        $request->validate([
            'mailbox_number' => 'required|string',
            'customer_name' => 'required|string',
            'message' => 'required|string|max:1000',
            'send_sms' => 'required|boolean',
            'send_email' => 'required|boolean',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        // Convert string values to boolean if needed
        $sendSms = filter_var($request->send_sms, FILTER_VALIDATE_BOOLEAN);
        $sendEmail = filter_var($request->send_email, FILTER_VALIDATE_BOOLEAN);

        // Additional validation based on delivery methods
        if ($sendSms && (!$request->phone_number || $request->phone_number === 'N/A')) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number is required for SMS delivery'
            ], 422);
        }

        if ($sendEmail && (!$request->email || $request->email === 'N/A')) {
            return response()->json([
                'success' => false,
                'message' => 'Email address is required for email delivery'
            ], 422);
        }

        $results = [];
        $errors = [];

        // Determine message type from the message content or template
        $messageType = $this->detectMessageType($request->message);

        try {
            // Send SMS if requested and phone number is available
            if ($sendSms && $request->phone_number && $request->phone_number !== 'N/A') {
                try {
                    $cleanPhone = preg_replace('/\D/', '', $request->phone_number);

                    // Validate phone number length
                    if (strlen($cleanPhone) >= 10) {
                        // Format phone number for Twilio
                        if (strlen($cleanPhone) == 10) {
                            $cleanPhone = "+1{$cleanPhone}";
                        } elseif (!str_starts_with($cleanPhone, '+')) {
                            $cleanPhone = "+{$cleanPhone}";
                        } else {
                            $cleanPhone = "+{$cleanPhone}";
                        }

                        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
                        $message = $twilio->messages->create($cleanPhone, [
                            'from' => env('TWILIO_PHONE_NUMBER'),
                            'body' => $request->message
                        ]);

                        $results[] = "SMS sent successfully to {$cleanPhone}";
                        Log::info("Quick message SMS sent to {$cleanPhone}: {$request->message}");
                    } else {
                        $errors[] = 'Invalid phone number format (too short)';
                    }
                } catch (TwilioException $e) {
                    $errors[] = 'SMS sending failed: ' . $e->getMessage();
                    Log::error('Twilio SMS Error: ' . $e->getMessage());
                } catch (\Exception $e) {
                    $errors[] = 'SMS error: ' . $e->getMessage();
                    Log::error('SMS sending error: ' . $e->getMessage());
                }
            }

            // Send Email if requested and email address is available
            if ($sendEmail && $request->email && $request->email !== 'N/A') {
                try {
                    Mail::to($request->email)->send(new QuickMessage(
                        $request->customer_name,
                        $request->mailbox_number,
                        $request->message,
                        'general',
                        'Mail Center'
                    ));
                    $results[] = 'Email sent successfully';
                } catch (\Exception $e) {
                    $errors[] = 'Email sending failed: ' . $e->getMessage();
                    Log::error('Email Error: ' . $e->getMessage());
                }
            }

            // Check if at least one method was attempted
            if (empty($results) && empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No delivery method selected or contact information missing'
                ], 400);
            }

            // Prepare response
            if (!empty($errors) && empty($results)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message sending failed: ' . implode(', ', $errors)
                ], 500);
            }

            $message = implode(' and ', $results);
            if (!empty($errors)) {
                $message .= ', but with some errors: ' . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'results' => $results,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Quick Message Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while sending the message'
            ], 500);
        }
    }

    /**
     * Send renewal reminder via SMS and/or Email
     */
    public function sendRenewalReminder(Request $request)
    {
        $request->validate([
            'mailbox_number' => 'required|string',
            'customer_name' => 'required|string',
            'due_date' => 'required|string',
            'message' => 'required|string|max:1000',
            'reminder_type' => 'required|string|in:gentle,standard,urgent,final,custom',
            'send_sms' => 'required|boolean',
            'send_email' => 'required|boolean',
        ]);

        // Convert string values to boolean if needed
        $sendSms = filter_var($request->send_sms, FILTER_VALIDATE_BOOLEAN);
        $sendEmail = filter_var($request->send_email, FILTER_VALIDATE_BOOLEAN);

        $results = [];
        $errors = [];

        try {
            // Get customer contact info from CSV (similar to QuickMessage)
            $phoneNumber = $request->phone_number ?? null;
            $email = null;

            // Look up email from company-specific CSV if mailbox is provided
            $mailboxNumber = $request->mailbox_number;
            if ($mailboxNumber) {
                $currentCompanyId = session('current_company_id') ?? Auth::user()->company_id;
                if ($currentCompanyId) {
                    $filePath = "uploads/company_{$currentCompanyId}_latest_file.csv";
                    if (\Storage::exists($filePath)) {
                        $data = $this->parseFile(\Storage::path($filePath));
                        foreach ($data as $row) {
                            if (isset($row[0]) && trim($row[0]) == trim($mailboxNumber)) {
                                if (isset($row[8]) && !empty(trim($row[8]))) {
                                    $email = trim($row[8]); // Email is typically in column 8
                                    Log::info("Email found for renewal reminder mailbox {$mailboxNumber}: {$email}");
                                }
                                break;
                            }
                        }
                    }
                }
            }

            // Send SMS if requested and phone number is available
            if ($sendSms && $phoneNumber && $phoneNumber !== 'N/A') {
                try {
                    $cleanPhone = preg_replace('/\D/', '', $phoneNumber);
                    if (strlen($cleanPhone) >= 10) {
                        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
                        $twilio->messages->create($cleanPhone, [
                            'from' => env('TWILIO_PHONE_NUMBER'),
                            'body' => $request->message
                        ]);
                        $results[] = 'SMS reminder sent';
                    } else {
                        $errors[] = 'Invalid phone number format';
                    }
                } catch (TwilioException $e) {
                    $errors[] = 'SMS reminder failed: ' . $e->getMessage();
                    Log::error('Renewal SMS Error: ' . $e->getMessage());
                }
            }

            // Send Email if requested and email address is available
            if ($sendEmail && $email && $email !== 'N/A') {
                try {
                    $companyName = Auth::user()->company ? Auth::user()->company->name : 'Mail Center';
                    Mail::to($email)->send(new RenewalReminder(
                        $request->customer_name,
                        $request->mailbox_number,
                        $request->due_date,
                        $request->message,
                        $request->reminder_type,
                        $companyName
                    ));
                    $results[] = 'Email reminder sent';
                } catch (\Exception $e) {
                    $errors[] = 'Email reminder failed: ' . $e->getMessage();
                    Log::error('Renewal Email Error: ' . $e->getMessage());
                }
            }

            // Check if at least one method was attempted
            if (empty($results) && empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No delivery method selected or contact information missing'
                ], 400);
            }

            // Prepare response
            if (!empty($errors) && empty($results)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Renewal reminder failed: ' . implode(', ', $errors)
                ], 500);
            }

            $message = 'Renewal reminder: ' . implode(' and ', $results);
            if (!empty($errors)) {
                $message .= ', but with some errors: ' . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'results' => $results,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Renewal Reminder Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while sending the renewal reminder'
            ], 500);
        }
    }

    /**
     * Parse CSV file for customer data lookup
     */
    private function parseFile($filePath) {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    /**
     * Detect message type based on content
     */
    private function detectMessageType($message)
    {
        $message = strtolower($message);

        if (strpos($message, 'package') !== false && strpos($message, 'pickup') !== false) {
            return 'package_ready';
        }
        if (strpos($message, 'payment') !== false || strpos($message, 'balance') !== false) {
            return 'payment_reminder';
        }
        if (strpos($message, 'update') !== false && strpos($message, 'account') !== false) {
            return 'account_update';
        }
        if (strpos($message, 'office hours') !== false || strpos($message, 'hours') !== false) {
            return 'office_hours';
        }

        return 'general';
    }
}
