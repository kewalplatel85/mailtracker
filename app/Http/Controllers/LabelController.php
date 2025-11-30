<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LabelController extends Controller
{
    /**
     * Display label printing page with mailboxes from uploaded CSV
     */
    public function index(Request $request)
    {
        // Get current company ID for multi-tenancy
        $currentCompanyId = session('current_company_id') ?? Auth::user()->company_id;
        $filePath = "uploads/company_{$currentCompanyId}_latest_file.csv";
        $csvData = [];

        // Check if the company-specific CSV file exists and load its contents
        if (Storage::exists($filePath)) {
            $csvData = $this->parseFile(Storage::path($filePath));
        }

        $mailboxes = collect();

        if (!empty($csvData)) {
            // Skip header rows and process data (assuming headers are in row 6 and data starts from row 7)
            $dataRows = array_slice($csvData, 7);

            foreach ($dataRows as $row) {
                if (!empty($row[0])) { // Make sure mailbox number exists
                    $mailboxNumber = trim($row[0]);
                    $customerName = isset($row[3]) ? trim($row[3]) : '';
                    $phoneNumber = isset($row[4]) ? trim($row[4]) : '';

                    // Apply filters if provided
                    $include = true;

                    if ($request->has('mailbox_number') && !empty($request->mailbox_number)) {
                        $include = $include && (strpos($mailboxNumber, $request->mailbox_number) !== false);
                    }

                    if ($request->has('customer_name') && !empty($request->customer_name)) {
                        $include = $include && (stripos($customerName, $request->customer_name) !== false);
                    }

                    if ($request->has('phone_number') && !empty($request->phone_number)) {
                        $include = $include && (strpos($phoneNumber, $request->phone_number) !== false);
                    }

                    if ($include) {
                        $mailboxes->push((object) [
                            'id' => $mailboxNumber, // Use mailbox number as ID
                            'mailbox_number' => $mailboxNumber,
                            'customer_name' => $customerName,
                            'phone_number' => $phoneNumber,
                            'created_at' => now(), // Use current date for expiry calculation
                        ]);
                    }
                }
            }
        }

        return view('labels.print', [
            'packages' => $mailboxes, // Using 'packages' variable name to maintain compatibility with view
            'filters' => $request->all()
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

    /**
     * Generate label for specific package
     */
    public function generateSingle($id)
    {
        $package = Package::findOrFail($id);

        return view('labels.single', [
            'package' => $package
        ]);
    }

    /**
     * Generate preview label for temporary package data (before saving)
     */
    public function generatePreview(Request $request)
    {
        // Check if package_data is provided as JSON (from JavaScript)
        if ($request->has('package_data')) {
            $packageData = json_decode($request->package_data, true);
        } else {
            // Validate required fields for direct form submission
            $request->validate([
                'tracking_number' => 'required|string',
                'customer_name' => 'required|string',
                'mailbox_number' => 'nullable|string',
                'status' => 'nullable|string',
            ]);

            $packageData = [
                'tracking_number' => $request->tracking_number,
                'customer_name' => $request->customer_name,
                'mailbox_number' => $request->mailbox_number,
                'status' => $request->status,
            ];
        }

        // Create a temporary package object (not saved to database)
        $tempPackage = (object) [
            'id' => 'PREVIEW',
            'tracking_number' => $packageData['tracking_number'],
            'customer_name' => $packageData['customer_name'],
            'mailbox_number' => $packageData['mailbox_number'] ?? 'N/A',
            'phone_number' => $packageData['phone_number'] ?? '',
            'status' => $packageData['status'] ?? 'Incoming',
            'created_at' => now(),
            'company_id' => Auth::check() ? (Auth::user()->company_id ?? 1) : 1,
            'is_preview' => true
        ];

        return view('labels.package-single', [
            'package' => $tempPackage
        ]);
    }

    /**
     * Generate preview labels for multiple temporary packages (before saving)
     */
    public function generatePreviewMultiple(Request $request)
    {
        try {
            // Handle both array format and JSON string format
            $packagesData = $request->input('packages');

            if (!$packagesData) {
                // Fallback to old format
                if ($request->has('packages_data')) {
                    $packagesData = json_decode($request->packages_data, true);
                } else {
                    // Return error response instead of abort for better debugging
                    return response()->json(['error' => 'No packages data provided. Expected "packages" array.', 'request_data' => $request->all()], 400);
                }
            }

            if (!is_array($packagesData) || empty($packagesData)) {
                return response()->json(['error' => 'Invalid packages data format. Expected non-empty array.', 'data' => $packagesData, 'type' => gettype($packagesData)], 400);
            }

            $tempPackages = [];
            foreach ($packagesData as $index => $packageData) {
                // Ensure packageData is an array
                if (!is_array($packageData)) {
                    continue;
                }

                $tempPackages[] = (object) [
                    'id' => 'PREVIEW_' . ($index + 1),
                    'tracking_number' => $packageData['tracking_number'] ?? 'UNKNOWN',
                    'customer_name' => $packageData['customer_name'] ?? 'Unknown Customer',
                    'mailbox_number' => $packageData['mailbox_number'] ?? 'N/A',
                    'phone_number' => $packageData['phone_number'] ?? '',
                    'status' => $packageData['status'] ?? 'Incoming',
                    'created_at' => now(),
                    'company_id' => Auth::check() ? (Auth::user()->company_id ?? 1) : 1,
                    'is_preview' => true
                ];
            }

            if (empty($tempPackages)) {
                return response()->json(['error' => 'No valid packages found to generate labels.'], 400);
            }

            // Generate standalone HTML like storage labels
            $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Package Labels</title>
    <style>
        @page {
            size: 4in 6in;
            margin: 0.1in;
        }

        * {
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .label-item {
            width: 4in;
            height: 6in;
            margin: 0;
            padding: 0.2in;
            border: none;
            page-break-after: always;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            background: white;
            box-sizing: border-box;
        }

        .label-item:last-child {
            page-break-after: avoid;
        }

        .package-header {
            width: 100%;
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 6pt;
            margin-bottom: 8pt;
        }

        .company-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 3pt;
            color: #000;
        }

        .package-title {
            font-size: 12pt;
            font-weight: 600;
            color: #000;
        }

        .tracking-section {
            width: 100%;
            margin-bottom: 8pt;
        }

        .tracking-label {
            font-size: 10pt;
            font-weight: 600;
            margin-bottom: 4pt;
            color: #000;
        }

        .tracking-number {
            font-size: 16pt;
            font-weight: bold;
            color: #000;
            font-family: monospace;
            border: 1px solid #000;
            padding: 3pt 6pt;
            background: #f9f9f9;
        }

        .customer-section {
            width: 100%;
            margin-bottom: 8pt;
        }

        .customer-name {
            font-size: 18pt;
            font-weight: 600;
            margin-bottom: 4pt;
            color: #000;
        }

        .mailbox-info {
            font-size: 14pt;
            color: #000;
            margin-bottom: 3pt;
        }

        .phone-info {
            font-size: 12pt;
            color: #000;
            font-family: monospace;
        }

        .barcode-section {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
        }

        .barcode-section svg {
            width: 2.8in;
            height: 0.7in;
        }
    </style>
</head>
<body>';

            foreach ($tempPackages as $package) {
                $companyName = isset($package->company) && $package->company ? $package->company : 'Mail All Center';
                $barcodeHtml = '';

                if (class_exists('Milon\Barcode\Facades\DNS1DFacade')) {
                    $barcodeHtml = \Milon\Barcode\Facades\DNS1DFacade::getBarcodeHTML($package->tracking_number, 'C128', 2.5, 80);
                } else {
                    $barcodeHtml = '<div style="height: 80px; border: 2px solid #000; display: flex; align-items: center; justify-content: center; font-family: monospace; font-size: 24px; background: #fff; color: #000;">*' . $package->tracking_number . '*</div>';
                }

                $mailboxInfo = ($package->mailbox_number && $package->mailbox_number !== 'N/A') ? '<div class="mailbox-info">Mailbox: ' . $package->mailbox_number . '</div>' : '';
                $phoneInfo = $package->phone_number ? '<div class="phone-info">' . $package->phone_number . '</div>' : '';

                $html .= '
    <div class="label-item">
        <div class="package-header">
            <div class="company-name">' . $companyName . '</div>
            <div class="package-title">Package Details</div>
        </div>

        <div class="tracking-section">
            <div class="tracking-label">Tracking Number:</div>
            <div class="tracking-number">' . $package->tracking_number . '</div>
        </div>

        <div class="customer-section">
            <div class="customer-name">' . $package->customer_name . '</div>
            ' . $mailboxInfo . '
            ' . $phoneInfo . '
        </div>

        <div class="barcode-section">
            ' . $barcodeHtml . '
        </div>
    </div>';
            }

            $html .= '
</body>
</html>';

            return response($html)->header('Content-Type', 'text/html');

        } catch (\Exception $e) {
            Log::error('Error in generatePreviewMultiple: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}
