<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Package;
use Illuminate\Support\Facades\Auth;

echo "Debugging Package Counts:\n\n";

// Check total packages
$totalPackages = Package::count();
echo "Total packages in database: {$totalPackages}\n";

// Check packages by company
$packagesByCompany = Package::select('company_id', \DB::raw('count(*) as count'))
    ->groupBy('company_id')
    ->get();

echo "\nPackages by company:\n";
foreach($packagesByCompany as $pkg) {
    echo "Company ID: {$pkg->company_id} | Count: {$pkg->count}\n";
}

// Check packages with mailbox numbers
$packagesWithMailbox = Package::whereNotNull('mailbox_number')->count();
echo "\nPackages with mailbox numbers: {$packagesWithMailbox}\n";

// Check distinct mailboxes with packages
$distinctMailboxes = Package::whereNotNull('mailbox_number')
    ->distinct('mailbox_number')
    ->count('mailbox_number');
echo "Distinct mailboxes with packages: {$distinctMailboxes}\n";

// Show some sample package data
$samplePackages = Package::limit(5)->get(['id', 'company_id', 'mailbox_number', 'customer_name', 'tracking_number']);
echo "\nSample packages:\n";
foreach($samplePackages as $pkg) {
    echo "ID: {$pkg->id} | Company: {$pkg->company_id} | Mailbox: {$pkg->mailbox_number} | Customer: {$pkg->customer_name}\n";
}
