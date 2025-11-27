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
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dashboard.view')->only(['index']);
        $this->middleware('permission:reports.view')->only(['getReports']);
        $this->middleware('permission:files.upload')->only(['upload']);
    }

    public function index(){
        $currentUser = auth()->user();
        $filePath = 'uploads/latest_file.csv';
        $data = [];

        // Check if the file exists and load its contents
        if (Storage::exists($filePath)) {
            $data = $this->parseFile(Storage::path($filePath));
        }

        // Get company-scoped statistics
        $stats = $this->getCompanyStats();

        // Instantiate MessageController and fetch SMS messages
        $messagesController = new MessageController();
        $inboxData = $messagesController->index();

        $receivedMessages = $inboxData['receivedMessages'];
        $sentMessages = $inboxData['sentMessages'];

        return view('dashboard', [
            'data' => $data,
            'receivedMessages' => $receivedMessages,
            'sentMessages' => $sentMessages,
            'stats' => $stats,
            'user' => $currentUser
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
                'tracking_numbers' => 'required|array',
                'tracking_numbers.*' => 'required|string',
                'customer_name' => 'required|string',
                'customer_email' => 'nullable|email',
                'package_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:10120',
                'captured_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:10120',
                'package_status' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        $imagePaths = [];
        $publicUrls = [];

        if (!Storage::disk('public')->exists('attachments')) {
            Storage::disk('public')->makeDirectory('attachments');
        }

       // 📷 Handle images from webcam
        if ($request->hasFile('captured_images')) {
            foreach ($request->file('captured_images') as $image) {
                $filename = uniqid('captured_') . '.' . $image->getClientOriginalExtension();
                $storedPath = $image->storeAs('attachments', $filename, 'public');
                $imagePaths[] = storage_path("app/public/{$storedPath}");
                $publicUrls[] = Storage::url($storedPath);
            }
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

        $trackingNumbers = $request->tracking_numbers;

        // ✅ Check if it's a new client
        $mailbox = $request->mailbox;
        if (!$mailbox) {
            $mailbox = null; // Allow null for new clients
        } else {
            $request->validate([
                'mailbox' => 'required|integer',
            ]);
        }

        $customerPhone = $request->customer_phone;
        if(!$customerPhone){
            $customerPhone = null;
        }else{
            $request->validate([
               'customer_phone' => 'string|min:10|max:15',
            ]);
            $customerPhone = preg_replace('/\D/', '', $request->customer_phone); // Remove non-numeric characters
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
            // Email with image attachment
            if ($request->filled('customer_email') && filter_var($request->customer_email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($request->customer_email)->send(
                    new \App\Mail\PackageNotification(
                        $imagePaths,
                        $request->customer_name,
                        $trackingNumbers,
                        null,
                        $publicUrls
                    )
                );
            }
        // Send SMS if customer phone is provided
            if($customerPhone != null){
                $trackingList = implode(", ", $trackingNumbers);
                // Send SMS using Twilio
                $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
                $twilio->messages->create($customerPhone, [
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => "Hi {$request->customer_name},{$request->sms} Tracking Number: {$trackingList}."
                ]);
            }

        return response()->json(['message' => 'Package saved and notifications sent successfully.']);
    }

    /**
     * Get statistics based on user's company scope
     */
    private function getCompanyStats()
    {
        $currentUser = auth()->user();
        
        // For super admins, use selected company or show global stats
        if ($currentUser->is_super_admin) {
            $companyId = session('selected_company_id');
            
            if ($companyId) {
                // Company-specific stats for super admin
                $packages = Package::where('company_id', $companyId);
                $companyName = \App\Models\Company::find($companyId)->name ?? 'Unknown';
            } else {
                // Global stats for super admin
                $packages = Package::query();
                $companyName = 'All Companies';
            }
        } else {
            // Company-specific stats for regular users
            $companyId = $currentUser->company_id;
            $packages = Package::where('company_id', $companyId);
            $companyName = $currentUser->company->name ?? 'Unknown';
        }

        // Calculate statistics
        $totalPackages = $packages->count();
        $pendingPackages = (clone $packages)->where('status', 'pending')->count();
        $shippedPackages = (clone $packages)->where('status', 'shipped')->count();
        $deliveredPackages = (clone $packages)->where('status', 'delivered')->count();
        
        // Recent packages (last 7 days)
        $recentPackages = (clone $packages)->where('created_at', '>=', now()->subDays(7))->count();
        
        // Additional company stats if super admin
        $companyStats = [];
        if ($currentUser->is_super_admin && !$companyId) {
            $companyStats = [
                'total_companies' => \App\Models\Company::where('status', 'active')->count(),
                'total_users' => \App\Models\User::whereHas('company', function($q) {
                    $q->where('status', 'active');
                })->count(),
            ];
        }

        return [
            'company_name' => $companyName,
            'total_packages' => $totalPackages,
            'pending_packages' => $pendingPackages,
            'shipped_packages' => $shippedPackages,
            'delivered_packages' => $deliveredPackages,
            'recent_packages' => $recentPackages,
            'company_stats' => $companyStats
        ];
    }

}
