<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        // Save the file locally
        $filePath = 'uploads/latest_file.csv';

        if (!Storage::exists('uploads')) {
            Storage::makeDirectory('uploads');
        }

        if (!$filePath) {
            return back()->withErrors('File upload failed!');
        }
        Storage::put($filePath, file_get_contents($request->file('file')));
        // Parse CSV or Excel
        $data = $this->parseFile(Storage::path($filePath));

        return view('dashboard',
            ['data' => $data,
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
        // Log::info('HIT updateCsv', $request->all());
        $path = storage_path('app/private/uploads/latest_file.csv');

        if (!file_exists($path)) {
            return response()->json(['message' => 'CSV file not found.'], 404);
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
