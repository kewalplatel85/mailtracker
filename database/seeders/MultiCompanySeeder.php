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
        // Create demo company
        $company = Company::create([
            'name' => 'Demo Mail Center',
            'slug' => 'demo-mail-center',
            'subdomain' => 'demo',
            'email' => 'admin@demo.mailtracker.com',
            'phone' => '555-123-4567',
            'address' => '123 Demo Street, Demo City, DC 12345',
            'status' => 'active',
            'subscription_plan' => 'premium',
        ]);

        // Create system roles (no company_id)
        $superAdminRole = Role::create([
            'name' => 'Super Administrator',
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
                'packages.view_all',
                'company.view',
                'company.edit',
                'company.settings',
                'roles.view',
                'roles.create',
                'roles.edit',
                'roles.delete',
                'reports.view',
                'reports.export',
                'messages.send',
                'messages.view',
            ],
            'is_system_role' => true,
            'company_id' => null,
        ]);

        // Create company-specific roles
        $companyAdminRole = Role::create([
            'name' => 'Company Administrator',
            'slug' => 'company-admin',
            'description' => 'Full access to company data and settings',
            'permissions' => [
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
                'packages.view',
                'packages.create',
                'packages.edit',
                'packages.delete',
                'packages.view_all',
                'company.view',
                'company.edit',
                'company.settings',
                'roles.view',
                'roles.create',
                'roles.edit',
                'roles.delete',
                'reports.view',
                'reports.export',
                'messages.send',
                'messages.view',
            ],
            'is_system_role' => false,
            'company_id' => $company->id,
        ]);

        $managerRole = Role::create([
            'name' => 'Manager',
            'slug' => 'manager',
            'description' => 'Manage packages and view reports',
            'permissions' => [
                'users.view',
                'packages.view',
                'packages.create',
                'packages.edit',
                'packages.view_all',
                'company.view',
                'reports.view',
                'reports.export',
                'messages.send',
                'messages.view',
            ],
            'is_system_role' => false,
            'company_id' => $company->id,
        ]);

        $employeeRole = Role::create([
            'name' => 'Employee',
            'slug' => 'employee',
            'description' => 'Basic package operations',
            'permissions' => [
                'packages.view',
                'packages.create',
                'packages.edit',
                'company.view',
                'messages.send',
                'messages.view',
            ],
            'is_system_role' => false,
            'company_id' => $company->id,
        ]);

        $clientRole = Role::create([
            'name' => 'Client',
            'slug' => 'client',
            'description' => 'View own packages only',
            'permissions' => [
                'packages.view',
                'company.view',
            ],
            'is_system_role' => false,
            'company_id' => $company->id,
        ]);

        // Create super admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@mailtracker.com',
            'username' => 'superadmin',
            'password' => Hash::make('password123'),
            'is_super_admin' => true,
            'company_id' => null,
            'email_verified_at' => now(),
        ]);

        // Create company admin user
        $companyAdmin = User::create([
            'name' => 'Demo Admin',
            'email' => 'admin@demo.mailtracker.com',
            'username' => 'demoadmin',
            'password' => Hash::make('password123'),
            'is_super_admin' => false,
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        // Create manager user
        $manager = User::create([
            'name' => 'Demo Manager',
            'email' => 'manager@demo.mailtracker.com',
            'username' => 'demomanager',
            'password' => Hash::make('password123'),
            'is_super_admin' => false,
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        // Create employee user
        $employee = User::create([
            'name' => 'Demo Employee',
            'email' => 'employee@demo.mailtracker.com',
            'username' => 'demoemployee',
            'password' => Hash::make('password123'),
            'is_super_admin' => false,
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        // Assign roles to users
        $companyAdmin->assignRole($companyAdminRole->id, $company->id);
        $manager->assignRole($managerRole->id, $company->id);
        $employee->assignRole($employeeRole->id, $company->id);

        $this->command->info('Multi-company system seeded successfully!');
        $this->command->info('Super Admin: superadmin@mailtracker.com / password123');
        $this->command->info('Company Admin: admin@demo.mailtracker.com / password123');
        $this->command->info('Manager: manager@demo.mailtracker.com / password123');
        $this->command->info('Employee: employee@demo.mailtracker.com / password123');
    }
}
