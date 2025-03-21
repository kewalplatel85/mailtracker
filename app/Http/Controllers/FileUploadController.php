<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
}
