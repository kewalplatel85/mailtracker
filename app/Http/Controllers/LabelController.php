<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class LabelController extends Controller
{
    /**
     * Display label printing page with mailboxes from uploaded CSV
     */
    public function index(Request $request)
    {
        $filePath = 'uploads/latest_file.csv';
        $csvData = [];

        // Check if the CSV file exists and load its contents
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
     * Generate PDF labels for selected packages
     */
    public function generatePdf(Request $request)
    {
        $packageIds = $request->input('package_ids', []);

        if (empty($packageIds)) {
            return redirect()->back()->with('error', 'No packages selected for printing.');
        }

        $packages = Package::whereIn('id', $packageIds)->get();

        $pdf = Pdf::loadView('labels.pdf', compact('packages'))
                  ->setPaper([0, 0, 288, 432], 'portrait') // 4x6 inches in points (72 points per inch)
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                  ]);

        return $pdf->download('storage-labels-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    /**
     * Generate single PDF label
     */
    public function generateSinglePdf($id)
    {
        $package = Package::findOrFail($id);

        $pdf = Pdf::loadView('labels.pdf-single', compact('package'))
                  ->setPaper([0, 0, 288, 432], 'portrait') // 4x6 inches in points
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                  ]);

        return $pdf->download('storage-label-' . $package->mailbox_number . '-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }
}
