<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;
use App\Models\Package;
use Illuminate\Routing\Controller as BaseController;

class PackageController extends BaseController
{
    public function __construct()
    {
        // Apply auth middleware to all methods except getPackagesByMailbox (for dashboard integration)
        $this->middleware('auth')->except(['getPackagesByMailbox']);
        // Remove restrictive permissions for basic package viewing
        // $this->middleware('permission:packages.view')->only(['index', 'show']);
        // $this->middleware('permission:packages.create')->only(['create', 'store']);
        // $this->middleware('permission:packages.edit')->only(['edit', 'update']);
        // $this->middleware('permission:packages.delete')->only(['destroy']);
        // $this->middleware('permission:packages.bulk_operations')->only(['bulkUpdate', 'bulkDelete', 'bulkSms']);
    }

    public function index()
    {
        // Packages are automatically scoped by company via global scope
        $packageLogs = Package::select(
            'mailbox_number',
            'customer_name',
            'phone_number',
            'status',
            'created_at',
            'tracking_number',
            'id'
        )
        ->where('status', 'Ready for Pickup')
        ->get()
        ->groupBy('mailbox_number')
        ->map(function ($group) {
            return (object) [
                'mailbox_number' => $group->first()->mailbox_number,
                'customer_name' => $group->first()->customer_name,
                'phone_number' => $group->first()->phone_number,
                'status' => $group->first()->status,
                'date_received' => \Carbon\Carbon::parse($group->first()->created_at)->format('d-m-Y'),
                'package_count' => $group->count(),
                'tracking_numbers' => $group->pluck('tracking_number')->toArray(),
                'id' => $group->pluck('id')->toArray(),
            ];
        });

        $messagesController = new MessageController();
        $inboxData = $messagesController->index();

        return view('packagelogs', [
            'packages' => $packageLogs,
            'receivedMessages' => $inboxData['receivedMessages'],
            'sentMessages' => $inboxData['sentMessages'],
        ]);
    }

    public function getPackages(Request $request)
    {
        $status = $request->query('status', 'Incoming');
        $packages = Package::where('status', $status)
            ->get()
            ->groupBy('mailbox_number')
            ->map(function ($group) {
                return [
                    'id' => $group->pluck('id')->toArray(),
                    'mailbox_number' => $group->first()->mailbox_number,
                    'customer_name' => $group->first()->customer_name,
                    'phone_number' => $group->first()->phone_number,
                    'package_count' => $group->count(),
                    'tracking_numbers' => $group->pluck('tracking_number')->toArray(),
                    'status' => $group->first()->status,
                    'date_received' => $group->first()->created_at->format('Y-m-d'),
                ];
            })
            ->values();

        return response()->json($packages);
    }

    public function getPackagesByMailbox($mailboxNumber)
    {
        try {
            // Check if user is authenticated, but don't fail if not (for dashboard integration)
            $packages = Package::where('mailbox_number', $mailboxNumber)
                ->whereIn('status', ['Incoming', 'Ready for Pickup', 'Picked Up'])
                ->orderBy('created_at', 'desc')
                ->get();

            $result = [];
            foreach ($packages as $index => $package) {
                $result[] = [
                    'id' => $package->id,
                    'tracking_number' => $package->tracking_number ?? 'N/A',
                    'status' => $package->status ?? 'Unknown',
                    'created_at' => $package->created_at ? $package->created_at->format('M d, Y') : 'Unknown',
                    'customer_name' => $package->customer_name ?? 'N/A',
                    'phone_number' => $package->phone_number ?? 'N/A',
                    // Add workflow information
                    'received_at' => $package->received_at ? $package->received_at->format('M d, Y H:i') : null,
                    'ready_at' => $package->ready_at ? $package->ready_at->format('M d, Y H:i') : null,
                    'picked_up_at' => $package->picked_up_at ? $package->picked_up_at->format('M d, Y H:i') : null,
                    'age_days' => method_exists($package, 'getAgeInDays') ? $package->getAgeInDays() : 0,
                ];
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error fetching packages by mailbox: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Failed to fetch packages: ' . $e->getMessage()
            ], 500);
        }
    }    public function checkTrackingNumberExist(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:255',
        ]);

        $tracking = Package::where('tracking_number', $request->tracking_number)->first();

        return response()->json([
            'exists' => (bool) $tracking,
            'status' => optional($tracking)->status,
            'customer_name' => optional($tracking)->customer_name,
            'mailbox_number' => optional($tracking)->mailbox_number,
        ]);
    }

    public function outgoingPackage(Request $request)
    {
        $request->validate([
            'tracking_numbers' => 'required|array',
            'package_status' => 'required|string',
            'customer_name' => 'required|string',
            'sms' => 'required|string',
        ]);

        $customerPhone = $request->customer_phone;

        if ($customerPhone) { // Only validate and clean if phone exists
            $request->validate([
                'customer_phone' => 'required|string|min:10|max:15',
            ]);
            $customerPhone = preg_replace('/\D/', '', $customerPhone); // Remove non-numeric characters
        } else {
            $customerPhone = null;
        }

        $trackingNumbers = $request->tracking_numbers;

        Package::whereIn('tracking_number', $trackingNumbers)
            ->update(['status' => $request->package_status]);

        $trackingList = implode(", ", $trackingNumbers);

        // FIX: Check if customerPhone is NOT null before sending SMS
        if ($customerPhone) {
            try {
                $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
                $twilio->messages->create($customerPhone, [ // Ensure this is not null
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => "Hi {$request->customer_name}, {$request->sms} Tracking Number: {$trackingList}."
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error sending SMS: ' . $e->getMessage()]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Package successfully picked, SMS sent!']);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'packages' => 'required|array',
            'packages.*.id' => 'required|integer|exists:packages,id',
            'packages.*.tracking_number' => 'required|string',
            'status' => 'required|string|in:Outgoing',
            'sms' => 'required|string'
        ]);

        $firstPackage = null;
        $phone = '';
        $customer = '';
        $trackingNumbers = [];

        foreach ($request->packages as $packageData) {
            $package = Package::find($packageData['id']);

            if ($package) {
                $package->status = $request->status;
                $package->save();

                // Set only once for SMS
                if (!$firstPackage) {
                    $firstPackage = $package;
                    $phone = preg_replace('/\D/', '', $package->phone_number);
                    $customer = $package->customer_name;
                }

                $trackingNumbers[] = $packageData['tracking_number'];
            }
        }

        // Prepare message
        if ($firstPackage && $phone) {
            try {
                $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
                $messageBody = '';

                if (count($trackingNumbers) === 1) {
                    $messageBody = "Hi {$customer}, {$request->sms} Tracking Number: {$trackingNumbers[0]}.";
                } else {
                    $messageBody = "Hi {$customer}, {$request->sms} Total Packages Claimed: " . count($trackingNumbers) . ".";
                }

                $twilio->messages->create($phone, [
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => $messageBody
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error sending SMS: ' . $e->getMessage()]);
            }
        }

        return response()->json(['message' => 'All packages updated and SMS sent!']);
    }

    public function deletePackage(Request $request)
    {
        $packageId = $request->input('package_id'); // Individual delete
        $status = $request->input('status');

        if ($status !== "Outgoing") {
            return response()->json(['success' => false, 'message' => 'Only "Outgoing" packages can be deleted.'], 400);
        }

        if ($packageId) {
            // Delete a single package by ID
            $deleted = Package::where('id', $packageId)->where('status', 'Outgoing')->delete();

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Package deleted successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Package not found or already deleted.'], 404);
            }
        } else {
            // Bulk delete - Delete all "Outgoing" packages
            $deletedCount = Package::where('status', 'Outgoing')->delete();

            return response()->json([
                'success' => true,
                'message' => $deletedCount > 0
                    ? "Deleted all outgoing packages successfully."
                    : "No outgoing packages found to delete."
            ]);
        }
    }


    public function deleteAllOutgoing(Request $request)
    {
        try {
            // Delete all outgoing packages
            $deleted = Package::where('status', 'Outgoing')->delete();

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'All outgoing packages deleted successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'No outgoing packages found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting outgoing packages.']);
        }
    }

    public function getLastPackageID(): JsonResponse
    {
        $lastPackage = Package::latest('id')->first();
        return response()->json(['last_id' => $lastPackage ? $lastPackage->id : 0]);
    }

    public function markAsPickedUp(Request $request)
    {
        $request->validate([
            'package_id' => 'required|integer|exists:packages,id',
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => true, 'message' => 'Authentication required'], 401);
            }

            $package = Package::findOrFail($request->package_id);

            // Check if package belongs to current user's company
            if ($package->company_id !== $user->company_id) {
                return response()->json(['error' => true, 'message' => 'Unauthorized access to package'], 403);
            }

            if ($package->status !== 'Ready for Pickup') {
                return response()->json(['error' => true, 'message' => 'Package is not ready for pickup'], 400);
            }

            $package->update([
                'status' => 'Picked Up',
                'picked_up_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Package marked as picked up successfully',
                'package_id' => $package->id,
                'tracking_number' => $package->tracking_number
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking package as picked up: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Failed to update package status'], 500);
        }
    }

    public function bulkMarkAsPickedUp(Request $request)
    {
        $request->validate([
            'package_ids' => 'required|array',
            'package_ids.*' => 'integer|exists:packages,id',
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => true, 'message' => 'Authentication required'], 401);
            }

            $packageIds = $request->package_ids;
            $userCompanyId = $user->company_id;

            // Get packages that belong to the user's company and are ready for pickup
            $packages = Package::whereIn('id', $packageIds)
                ->where('company_id', $userCompanyId)
                ->where('status', 'Ready for Pickup')
                ->get();

            if ($packages->isEmpty()) {
                return response()->json(['error' => true, 'message' => 'No valid packages found for pickup'], 400);
            }

            // Update all packages
            $updatedCount = Package::whereIn('id', $packages->pluck('id'))
                ->update([
                    'status' => 'Picked Up',
                    'picked_up_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} packages marked as picked up successfully",
                'updated_count' => $updatedCount,
                'package_ids' => $packages->pluck('id')->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error bulk marking packages as picked up: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Failed to update package statuses'], 500);
        }
    }

}
