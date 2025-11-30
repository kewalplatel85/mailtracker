<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== Debugging Package Counts ===\n";

// Check all packages
$allPackages = Package::count();
echo "Total packages in database: {$allPackages}\n";

// Check packages by company
$packagesByCompany = Package::selectRaw('company_id, count(*) as count')
    ->groupBy('company_id')
    ->get();

echo "\nPackages by company:\n";
foreach($packagesByCompany as $stat) {
    echo "Company {$stat->company_id}: {$stat->count} packages\n";
}

// Check packages with mailbox numbers
$packagesWithMailbox = Package::whereNotNull('mailbox_number')
    ->selectRaw('company_id, count(distinct mailbox_number) as unique_mailboxes, count(*) as total_packages')
    ->groupBy('company_id')
    ->get();

echo "\nPackages with mailbox numbers:\n";
foreach($packagesWithMailbox as $stat) {
    echo "Company {$stat->company_id}: {$stat->unique_mailboxes} unique mailboxes, {$stat->total_packages} total packages\n";
}

// Check current user context
$testUserId = 2; // Assuming this is the test user
$user = User::find($testUserId);
if ($user) {
    echo "\nUser ID {$testUserId} details:\n";
    echo "Company ID: {$user->company_id}\n";
    echo "Is Super Admin: " . ($user->is_super_admin ? 'Yes' : 'No') . "\n";

    // Check packages for this user's company
    $userCompanyPackages = Package::where('company_id', $user->company_id)->count();
    echo "Packages for user's company ({$user->company_id}): {$userCompanyPackages}\n";
}
