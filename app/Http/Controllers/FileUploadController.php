<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MessageController;

class FileUploadController extends Controller
{
    //
    public function upload(Request $request){
        $messagesController = new MessageController();
        $inboxData = $messagesController->index();

        $receivedMessages = $inboxData['receivedMessages'];
        $sentMessages = $inboxData['sentMessages'];

        $request->validate([
            'file' => 'required|mimes:csv,xlsx|max:2048',
        ]);

        // CRITICAL: Get current company context
        $currentCompanyId = session('current_company_id') ?? Auth::user()->company_id;

        if (!$currentCompanyId && !Auth::user()->is_super_admin) {
            return back()->withErrors('Error: No company associated with this user. Contact administrator.');
        }

        if (Auth::user()->is_super_admin && !$currentCompanyId) {
            return back()->withErrors('Super Admin: Please select a company context before uploading files.');
        }        // Save the file with company-specific naming
        $filePath = "uploads/company_{$currentCompanyId}_latest_file.csv";

        if (!Storage::exists('uploads')) {
            Storage::makeDirectory('uploads');
        }

        if (!$filePath) {
            return back()->withErrors('File upload failed!');
        }
        Storage::put($filePath, file_get_contents($request->file('file')));

        // Parse CSV or Excel
        $data = $this->parseFile(Storage::path($filePath));

        // Calculate stats from the uploaded data for the current company
        $stats = [
            'total_mailboxes' => 0,
            'mailboxes_with_packages' => 0,
            'total_packages' => 0
        ];

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

        // Get total packages for this company
        $stats['total_packages'] = \App\Models\Package::where('company_id', $currentCompanyId)->count();

        return view('dashboard',
            ['data' => $data,
            'stats' => $stats,
            'receivedMessages' => $receivedMessages,
            'sentMessages' => $sentMessages]);
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

    public function updateCsv(Request $request)
    {
        // CRITICAL: Get current company context
        $currentCompanyId = session('current_company_id') ?? Auth::user()->company_id;

        if (!$currentCompanyId) {
            return response()->json(['message' => 'No company context found.'], 403);
        }

        // Use company-specific file path
        $path = storage_path("app/uploads/company_{$currentCompanyId}_latest_file.csv");

        if (!file_exists($path)) {
            return response()->json(['message' => 'Company CSV file not found. Please upload a file first.'], 404);
        }

        // Read CSV rows
        $rows = array_map('str_getcsv', file($path));

        // Start at line 7 to skip non-data
        for ($i = 7; $i < count($rows); $i++) {
        // Check if mailbox number matches (assumes it's in column 0)
            if (trim($rows[$i][0]) === trim($request->mailbox)) {

                // Update known indexes (adjust if your structure differs)
                $rows[$i][1] = $request->size ?? $rows[$i][1];          // Size/Type
                $rows[$i][2] = $request->status ?? $rows[$i][2];        // Status
                $rows[$i][3] = $request->customer;      // Customer
                $rows[$i][4] = $request->phone;         // Phone Number
                $rows[$i][5] = $request->date_close;    // Date Close
                $rows[$i][6] = $request->term;          // Term
                $rows[$i][7] = $request->due_date;      // Due Date
                $rows[$i][8] = $request->email;         // Email
                break;
            }
        }

        // Write updated content back to CSV
        $fp = fopen($path, 'w');
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        return response()->json(['message' => 'Mailbox #' . $request->mailbox . ' updated successfully.']);
    }
}
