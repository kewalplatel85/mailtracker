<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MultiCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or find Mail All Center as the default company (Company #1)
        $mailAllCenter = Company::where('slug', 'mail-all-center')->first();
        if (!$mailAllCenter) {
            $mailAllCenter = Company::create([
                'name' => 'Mail All Center',
                'slug' => 'mail-all-center',
                'subdomain' => 'mailallcenter',
                'email' => 'info@mailallcenter.com',
                'phone' => '555-MAIL-ALL',
                'address' => '123 Main Street, Anytown, ST 12345',
                'status' => 'active',
                'subscription_plan' => 'premium',
            ]);
        }

        // Create or find demo company for testing
        $demoCompany = Company::where('slug', 'demo-mail-center')->first();
        if (!$demoCompany) {
            $demoCompany = Company::create([
                'name' => 'Demo Mail Center',
                'slug' => 'demo-mail-center',
                'subdomain' => 'demo',
                'email' => 'admin@demo.mailtracker.com',
                'phone' => '555-123-4567',
                'address' => '123 Demo Street, Demo City, DC 12345',
                'status' => 'active',
                'subscription_plan' => 'premium',
            ]);
        }

        // Create super admin role (system-wide)
        $superAdminRole = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
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
            'company_id' => null,
        ]);

        // Create Mail All Center Admin role
        $mailAllAdminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
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
            'company_id' => $mailAllCenter->id,
        ]);

        // Create Mail All Center User role
        $mailAllUserRole = Role::create([
            'name' => 'User',
            'slug' => 'user',
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
            'company_id' => $mailAllCenter->id,
        ]);

        // Create Demo Company Admin role
        $demoAdminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Admin access for Demo Mail Center',
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
            'company_id' => $demoCompany->id,
        ]);

        // Create Demo Company User role
        $demoUserRole = Role::create([
            'name' => 'User',
            'slug' => 'user',
            'description' => 'User access for Demo Mail Center',
            'permissions' => [
                'packages.view',
                'packages.create',
                'packages.edit',
                'mailboxes.view',
                'dashboard.view',
                'messages.view',
            ],
            'is_system_role' => false,
            'company_id' => $demoCompany->id,
        ]);

        // Create or find default super admin user
        $defaultSuperAdmin = User::where('username', 'superadmin')->first();
        if (!$defaultSuperAdmin) {
            $defaultSuperAdmin = User::create([
                'name' => 'Default Super Admin',
                'email' => 'superadmin@mailtracker.com',
                'username' => 'superadmin',
                'password' => Hash::make('password123'),
                'is_super_admin' => true,
                'company_id' => null,
                'email_verified_at' => now(),
            ]);
        }

        // Update existing "patel" user to be company admin (not super admin)
        $patelUser = User::where('username', 'patel')->first();
        if ($patelUser) {
            $patelUser->update([
                'company_id' => $mailAllCenter->id,
                'is_super_admin' => false,
            ]);
        } else {
            $patelUser = User::create([
                'name' => 'Patel Admin',
                'email' => 'patel@mailallcenter.com',
                'username' => 'patel',
                'password' => Hash::make('password123'),
                'is_super_admin' => false,
                'company_id' => $mailAllCenter->id,
                'email_verified_at' => now(),
            ]);
        }

        // Update existing "khairo" user (Eldrin) to be super admin
        $eldrinUser = User::where('username', 'khairo')->first();
        if ($eldrinUser) {
            $eldrinUser->update([
                'is_super_admin' => true,
                'company_id' => null,
            ]);
        } else {
            $eldrinUser = User::create([
                'name' => 'Eldrin',
                'email' => 'eldrin.bradecina@gmail.com',
                'username' => 'khairo',
                'password' => Hash::make('password123'),
                'is_super_admin' => true,
                'company_id' => null,
                'email_verified_at' => now(),
            ]);
        }

        // Create or find company admin user for demo company
        $companyAdmin = User::where('username', 'demoadmin')->first();
        if (!$companyAdmin) {
            $companyAdmin = User::create([
                'name' => 'Demo Admin',
                'email' => 'admin@demo.mailtracker.com',
                'username' => 'demoadmin',
                'password' => Hash::make('password123'),
                'is_super_admin' => false,
                'company_id' => $demoCompany->id,
                'email_verified_at' => now(),
            ]);
        } else {
            $companyAdmin->update([
                'company_id' => $demoCompany->id,
                'is_super_admin' => false,
            ]);
        }

        // Assign roles to users
        $defaultSuperAdmin->assignRole($superAdminRole->id, null);
        $eldrinUser->assignRole($superAdminRole->id, null);
        $patelUser->assignRole($mailAllAdminRole->id, $mailAllCenter->id);
        $companyAdmin->assignRole($demoAdminRole->id, $demoCompany->id);

        // Create regular users for testing
        $mailAllUser = User::where('username', 'mailuser')->first();
        if (!$mailAllUser) {
            $mailAllUser = User::create([
                'name' => 'Mail All User',
                'email' => 'user@mailallcenter.com',
                'username' => 'mailuser',
                'password' => Hash::make('password123'),
                'is_super_admin' => false,
                'company_id' => $mailAllCenter->id,
                'email_verified_at' => now(),
            ]);
        }
        $mailAllUser->assignRole($mailAllUserRole->id, $mailAllCenter->id);

        $demoUser = User::where('username', 'demouser')->first();
        if (!$demoUser) {
            $demoUser = User::create([
                'name' => 'Demo User',
                'email' => 'user@demo.mailtracker.com',
                'username' => 'demouser',
                'password' => Hash::make('password123'),
                'is_super_admin' => false,
                'company_id' => $demoCompany->id,
                'email_verified_at' => now(),
            ]);
        }
        $demoUser->assignRole($demoUserRole->id, $demoCompany->id);

        $this->command->info('Multi-company system seeded successfully!');
        $this->command->info('Default Super Admin: superadmin@mailtracker.com / password123');
        $this->command->info('Eldrin (Super Admin): khairo / password123');
        $this->command->info('Mail All Center Admin (patel): patel / password123');
        $this->command->info('Mail All Center User: user@mailallcenter.com / password123');
        $this->command->info('Demo Company Admin: admin@demo.mailtracker.com / password123');
        $this->command->info('Demo Company User: user@demo.mailtracker.com / password123');
    }
}

