<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupTempImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-temp-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $files = Storage::disk('public')->files('temp_images');

        foreach ($files as $file) {
            $fullPath = storage_path("app/public/$file");
            if (file_exists($fullPath) && now()->diffInHours(filemtime($fullPath)) > 48) {
                Storage::disk('public')->delete($file);
            }
        }
    }
}
