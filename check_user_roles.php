<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;

echo "Checking user role assignments:\n";

// Get users with their role assignments
$users = User::with(['userRoles.role', 'company'])->get();

foreach($users as $user) {
    echo "\nUser: {$user->name} ({$user->email})\n";
    echo "Company: " . ($user->company ? $user->company->name : 'No Company') . "\n";
    echo "Is Super Admin: " . ($user->is_super_admin ? 'Yes' : 'No') . "\n";

    $userRoles = $user->userRoles()->with('role')->where('is_active', true)->get();
    echo "Active Roles: ";
    if ($userRoles->count() > 0) {
        foreach($userRoles as $userRole) {
            echo $userRole->role->name . " (Company: {$userRole->company_id}) ";
        }
    } else {
        echo "No active roles assigned";
    }
    echo "\n";
}

echo "\n\nChecking available roles:\n";
$roles = Role::all();
foreach($roles as $role) {
    echo "Role: {$role->name} | Slug: {$role->slug} | Company: " . ($role->company_id ?: 'System') . "\n";
}
