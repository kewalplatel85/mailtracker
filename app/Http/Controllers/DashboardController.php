<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use App\Models\Package;
use App\Http\Controllers\MessageController;
use App\Services\PackageWorkflowService;

class DashboardController extends Controller
{
    //
    public function index(){
        // CRITICAL: Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        // CRITICAL: Get current company context
        $currentCompanyId = session('current_company_id') ?? ($user ? $user->company_id : null);

        $data = [];

        // Initialize stats with default values
        $stats = [
            'total_mailboxes' => 0,
            'mailboxes_with_packages' => 0,
            'total_packages' => 0
        ];

        if ($currentCompanyId) {
            // Use company-specific file path
            $filePath = "uploads/company_{$currentCompanyId}_latest_file.csv";

            // Check if the file exists and load its contents
            if (Storage::exists($filePath)) {
                $data = $this->parseFile(Storage::path($filePath));

                // Calculate stats from CSV data
                if (count($data) > 7) { // Skip header rows
                    $mailboxData = array_slice($data, 7); // Skip first 7 rows (headers)

                    // Count only rows that have actual mailbox numbers (not empty rows)
                    $validMailboxes = 0;
                    foreach ($mailboxData as $row) {
                        // Check if first column (mailbox number) has actual data
                        if (isset($row[0]) && !empty(trim($row[0])) && is_numeric(trim($row[0]))) {
                            $validMailboxes++;
                        }
                    }
                    $stats['total_mailboxes'] = $validMailboxes;

                    // Count mailboxes that actually have packages in the database
                    $mailboxesWithPackages = \App\Models\Package::where('company_id', $currentCompanyId)
                        ->whereNotNull('mailbox_number')
                        ->distinct('mailbox_number')
                        ->count('mailbox_number');

                    $stats['mailboxes_with_packages'] = $mailboxesWithPackages;
                }
            }

            // Get total packages for this company
            $stats['total_packages'] = \App\Models\Package::where('company_id', $currentCompanyId)->count();

        } else if ($user && $user->is_super_admin) {
            // Super admin sees a message to select company context
            $data = [['Super Admin: Please select a company context to view mailbox data']];
            // Stats remain at 0 for super admin without company context
        }        // Instantiate MessageController and fetch SMS messages
        $messagesController = new MessageController();
        $inboxData = $messagesController->index();

        $receivedMessages = $inboxData['receivedMessages'];
        $sentMessages = $inboxData['sentMessages'];

        // Get current company for view
        $currentCompany = null;
        if ($currentCompanyId) {
            $currentCompany = \App\Models\Company::find($currentCompanyId);
        }

        return view('dashboard', [
            'data' => $data,
            'stats' => $stats,
            'receivedMessages' => $receivedMessages,
            'sentMessages' => $sentMessages,
            'currentCompany' => $currentCompany
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

    public function savePackage(Request $request, PackageWorkflowService $workflowService) {
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

        // CRITICAL: Ensure proper company assignment
        // CRITICAL: Ensure user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 401);
        }

        $user = Auth::user();
        $currentCompanyId = session('current_company_id') ?? ($user ? $user->company_id : null);

        if (!$currentCompanyId && !($user && $user->is_super_admin)) {
            return response()->json([
                'message' => 'Error: No company associated with this user. Contact administrator.'
            ], 403);
        }

        if ($user && $user->is_super_admin && !$currentCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a company from the navigation dropdown before adding packages.'
            ], 400);
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

        // Extract customer phone from company-specific CSV data
        $customerPhone = null;
        $phoneSource = null;
        if ($mailbox) {
            // Look up customer info from company-specific CSV data
            $filePath = "uploads/company_{$currentCompanyId}_latest_file.csv";
            if (Storage::exists($filePath)) {
                $data = $this->parseFile(Storage::path($filePath));
                // Search for mailbox in data to get phone number
                foreach ($data as $row) {
                    if (isset($row[0]) && trim($row[0]) == trim($mailbox)) {
                        if (isset($row[4]) && !empty(trim($row[4]))) {
                            $customerPhone = preg_replace('/\D/', '', trim($row[4]));
                            $phoneSource = 'CSV';
                            Log::info("Phone number found for mailbox {$mailbox}: {$customerPhone}");
                        } else {
                            Log::info("Mailbox {$mailbox} found but no phone number in CSV");
                        }
                        break;
                    }
                }
                if (!$customerPhone) {
                    Log::info("Mailbox {$mailbox} not found in CSV file");
                }
            } else {
                Log::warning("CSV file not found for company {$currentCompanyId}");
            }
        }

        $createdPackages = [];

        // Create packages based on package count with workflow support
        for ($i = 0; $i < $packageCount; $i++) {
            $trackingNumber = isset($trackingNumbers[$i]) ? $trackingNumbers[$i] : '';

            $package = Package::create([
                'customer_name' => $request->customer_name,
                'phone_number' => $customerPhone,
                'mailbox_number' => $mailbox,
                'tracking_number' => $trackingNumber,
                'status' => $request->status,
                'company_id' => $currentCompanyId, // CRITICAL: Explicit company assignment
                // Workflow fields
                'auto_ready' => true, // Enable auto-transition by default
                'days_to_ready' => 0, // Immediate transition
            ]);

            $createdPackages[] = $package;

            // If package is incoming, process auto-transition
            if ($request->status === 'Incoming') {
                $workflowService->processAutoTransitions();
            }
        }

        // Send SMS if customer phone is provided
        $smsResult = ['sent' => false, 'message' => 'No phone number found'];

        // Debug logging to understand why SMS is not sending
        Log::info("SMS Debug - customerPhone: " . ($customerPhone ?? 'null'));
        Log::info("SMS Debug - sms_message: " . ($request->sms_message ?? 'null'));
        Log::info("SMS Debug - mailbox: " . ($mailbox ?? 'null'));

        if ($customerPhone) {
            // Use default message if SMS message is empty or null
            $defaultMessage = "HI {$request->customer_name}, this Mail All center, You have a package ready for pick up. thanks.!";
            $smsMessage = !empty($request->sms_message) ? $request->sms_message : $defaultMessage;

            Log::info("SMS Debug - Using message: " . $smsMessage);
            // Validate phone number length
            if (strlen($customerPhone) < 10) {
                $smsResult = ['sent' => false, 'message' => 'Invalid phone number format (too short)'];
            } else {
                $trackingList = implode(", ", array_filter($trackingNumbers));
                $smsBody = $smsMessage;

                // Add tracking numbers to SMS if available
                if (!empty($trackingList)) {
                    $smsBody .= " Tracking Number(s): {$trackingList}.";
                }

                try {
                    // Clean phone number format
                    $cleanPhone = preg_replace('/\D/', '', $customerPhone);
                    if (strlen($cleanPhone) == 10) {
                        $cleanPhone = "+1{$cleanPhone}";
                    } elseif (!str_starts_with($cleanPhone, '+')) {
                        $cleanPhone = "+{$cleanPhone}";
                    }

                    // Send SMS using Twilio
                    $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
                    $message = $twilio->messages->create($cleanPhone, [
                        'from' => env('TWILIO_PHONE_NUMBER'),
                        'body' => $smsBody
                    ]);

                    $smsResult = ['sent' => true, 'message' => "SMS sent to {$cleanPhone} (found in {$phoneSource})"];

                } catch (TwilioException $e) {
                    $smsResult = ['sent' => false, 'message' => 'Twilio error: ' . $e->getMessage()];
                    Log::error('Twilio SMS sending failed: ' . $e->getMessage());
                } catch (\Exception $e) {
                    $smsResult = ['sent' => false, 'message' => 'SMS error: ' . $e->getMessage()];
                    Log::error('SMS sending error: ' . $e->getMessage());
                }
            }
        } elseif (!$customerPhone && $request->sms_message) {
            $smsResult = ['sent' => false, 'message' => $mailbox ? "No phone number found for mailbox {$mailbox}" : 'No phone number available (no mailbox specified)'];
            Log::info("SMS not sent - no phone number found for mailbox: {$mailbox}");
        }

        return response()->json([
            'success' => true,
            'message' => count($createdPackages) > 1 ?
                "{$packageCount} packages saved successfully!" :
                'Package saved successfully!',
            'packages_created' => $packageCount,
            'phone_found' => $customerPhone ? true : false,
            'phone_source' => $phoneSource ?? null,
            'customer_phone' => $customerPhone ? "+1" . substr($customerPhone, -10) : null,
            'sms_sent' => $smsResult['sent'],
            'sms_message' => $smsResult['message'],
            'mailbox_number' => $mailbox,
            'packages' => collect($createdPackages)->map(function($package) {
                return [
                    'id' => $package->id,
                    'tracking_number' => $package->tracking_number,
                    'customer_name' => $package->customer_name,
                    'mailbox_number' => $package->mailbox_number,
                ];
            }),
            'workflow_status' => $request->status === 'Incoming' ? 'Auto-transition enabled' : 'Manual workflow'
        ]);
    }}
