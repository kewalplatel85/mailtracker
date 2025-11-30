<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProductionSafeMultiCompanySeeder extends Seeder
{
    /**
     * Run the database seeds - Production Safe Version
     */
    public function run(): void
    {
        $this->command->info('Setting up multi-company system (production-safe)...');

        // Create or find Mail All Center as the default company
        $mailAllCenter = Company::firstOrCreate(
            ['slug' => 'mail-all-center'],
            [
                'name' => 'Mail All Center',
                'subdomain' => 'mailallcenter',
                'email' => 'info@mailallcenter.com',
                'phone' => '555-MAIL-ALL',
                'address' => '123 Main Street, Anytown, ST 12345',
                'status' => 'active',
                'subscription_plan' => 'premium',
            ]
        );

        // Create system roles only if they don't exist
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super-admin', 'company_id' => null],
            [
                'name' => 'Super Admin',
                'description' => 'Full system access across all companies',
                'permissions' => [
                    'system.manage_companies',
                    'system.manage_users',
                    'system.view_logs',
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',
                    'packages.view',
                    'packages.create',
                    'packages.edit',
                    'packages.delete',
                    'packages.bulk_operations',
                    'packages.view_all',
                    'packages.reports',
                    'packages.export',
                    'mailboxes.view',
                    'mailboxes.create',
                    'mailboxes.edit',
                    'mailboxes.delete',
                    'mailboxes.reports',
                    'company.view',
                    'company.edit',
                    'company.settings',
                    'roles.view',
                    'roles.create',
                    'roles.edit',
                    'roles.delete',
                    'dashboard.view',
                    'files.upload',
                    'messages.send',
                    'messages.view',
                ],
                'is_system_role' => true,
            ]
        );

        // Create Mail All Center roles
        $mailAllAdminRole = Role::firstOrCreate(
            ['slug' => 'admin', 'company_id' => $mailAllCenter->id],
            [
                'name' => 'Admin',
                'description' => 'Admin access for Mail All Center',
                'permissions' => [
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',
                    'packages.view',
                    'packages.create',
                    'packages.edit',
                    'packages.delete',
                    'packages.bulk_operations',
                    'packages.reports',
                    'packages.export',
                    'mailboxes.view',
                    'mailboxes.create',
                    'mailboxes.edit',
                    'mailboxes.delete',
                    'mailboxes.reports',
                    'company.view',
                    'company.edit',
                    'roles.view',
                    'dashboard.view',
                    'files.upload',
                    'messages.send',
                    'messages.view',
                ],
                'is_system_role' => false,
            ]
        );

        $mailAllUserRole = Role::firstOrCreate(
            ['slug' => 'user', 'company_id' => $mailAllCenter->id],
            [
                'name' => 'User',
                'description' => 'User access for Mail All Center',
                'permissions' => [
                    'packages.view',
                    'packages.create',
                    'packages.edit',
                    'mailboxes.view',
                    'dashboard.view',
                    'messages.view',
                ],
                'is_system_role' => false,
            ]
        );

        // Update existing users safely - only if they exist

        // Update existing "khairo" user (Eldrin) if exists
        $eldrinUser = User::where('username', 'khairo')->first();
        if ($eldrinUser) {
            $eldrinUser->update([
                'is_super_admin' => true,
                'company_id' => $mailAllCenter->id, // Assign to Mail All Center but mark as super admin
            ]);

            // Don't assign role - super admins use is_super_admin flag instead
            $this->command->info('Updated existing user: khairo (Eldrin) to Super Admin');
        } else {
            $this->command->info('User "khairo" not found - will need to be created manually');
        }

        // Update existing "patel" user if exists
        $patelUser = User::where('username', 'patel')->first();
        if ($patelUser) {
            $patelUser->update([
                'company_id' => $mailAllCenter->id,
                'is_super_admin' => false,
            ]);

            // Assign admin role if not already assigned
            if (!$patelUser->userRoles()->where('role_id', $mailAllAdminRole->id)->exists()) {
                $patelUser->assignRole($mailAllAdminRole->id, $mailAllCenter->id);
            }

            $this->command->info('Updated existing user: patel to Mail All Center Admin');
        } else {
            $this->command->info('User "patel" not found - will need to be created manually');
        }

        // Create default super admin only in development environments
        if (app()->environment(['local', 'development', 'testing'])) {
            $defaultSuperAdmin = User::firstOrCreate(
                ['username' => 'superadmin'],
                [
                    'name' => 'Default Super Admin',
                    'email' => 'superadmin@mailtracker.com',
                    'password' => Hash::make('password123'),
                    'is_super_admin' => true,
                    'company_id' => $mailAllCenter->id, // Assign to Mail All Center but mark as super admin
                    'email_verified_at' => now(),
                ]
            );

            // Don't assign role - super admins use is_super_admin flag instead
            $this->command->info('Created default superadmin user for development');
        }

        $this->command->info('Multi-company system setup completed successfully!');
        $this->command->info("Environment: " . app()->environment());

        if ($eldrinUser) {
            $this->command->info('✓ Eldrin (Super Admin): khairo / existing password');
        }

        if ($patelUser) {
            $this->command->info('✓ Patel (Mail All Center Admin): patel / existing password');
        }

        if (app()->environment(['local', 'development', 'testing'])) {
            $this->command->info('✓ Default Super Admin: superadmin@mailtracker.com / password123');
        }
    }
}
