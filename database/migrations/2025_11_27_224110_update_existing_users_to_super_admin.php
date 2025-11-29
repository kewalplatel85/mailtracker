<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing users to super admin based on their emails
        $superAdminEmails = [
            'eldrin.bradecina@gmail.com',
            'mailallcenter1@gmail.com'
        ];

        foreach ($superAdminEmails as $email) {
            User::where('email', $email)->update([
                'is_super_admin' => true,
                'company_id' => null, // Super admins don't belong to specific companies
                'updated_at' => now()
            ]);
        }

        // Also update by username if emails don't match
        $superAdminUsernames = [
            'khairo', // Eldrin's username
            'Patel'   // Kewal's username
        ];

        foreach ($superAdminUsernames as $username) {
            User::where('username', $username)->update([
                'is_super_admin' => true,
                'company_id' => null,
                'updated_at' => now()
            ]);
        }

        // Log the updates
        $updatedUsers = User::whereIn('email', $superAdminEmails)
                           ->orWhereIn('username', $superAdminUsernames)
                           ->get(['id', 'name', 'email', 'username', 'is_super_admin']);

        foreach ($updatedUsers as $user) {
            Log::info("Updated user to super admin", [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'is_super_admin' => $user->is_super_admin
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the super admin status for these specific users
        $emails = ['eldrin.bradecina@gmail.com', 'mailallcenter1@gmail.com'];
        $usernames = ['khairo', 'Patel'];

        User::whereIn('email', $emails)
            ->orWhereIn('username', $usernames)
            ->update([
                'is_super_admin' => false,
                'updated_at' => now()
            ]);
    }
};
