<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Create companies table if it doesn't exist
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('subdomain')->unique()->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
                $table->enum('subscription_plan', ['basic', 'premium', 'enterprise'])->default('basic');
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        }

        // Step 2: Create roles table if it doesn't exist
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->text('description')->nullable();
                $table->json('permissions')->nullable();
                $table->boolean('is_system_role')->default(false);
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->timestamps();

                $table->unique(['slug', 'company_id']);
                $table->index(['company_id', 'is_system_role']);
            });
        }

        // Step 3: Create user_roles table if it doesn't exist
        if (!Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->boolean('is_active')->default(true);
                $table->timestamp('assigned_at')->useCurrent();
                $table->timestamps();

                $table->unique(['user_id', 'role_id', 'company_id']);
                $table->index(['user_id', 'company_id']);
                $table->index(['role_id']);
            });
        }

        // Step 4: Add company_id to users table if it doesn't exist
        if (!Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('set null');
                $table->boolean('is_super_admin')->default(false)->after('password');
            });
        }

        // Step 5: Add company_id to packages table if it doesn't exist
        if (!Schema::hasColumn('packages', 'company_id')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->foreignId('created_by')->nullable()->after('company_id')->constrained('users')->onDelete('set null');
                $table->json('metadata')->nullable()->after('status');

                // Workflow fields
                $table->timestamp('received_at')->nullable()->after('metadata');
                $table->timestamp('ready_at')->nullable()->after('received_at');
                $table->timestamp('picked_up_at')->nullable()->after('ready_at');
                $table->timestamp('notified_at')->nullable()->after('picked_up_at');
                $table->boolean('auto_ready')->default(false)->after('notified_at');
                $table->integer('days_to_ready')->default(0)->after('auto_ready');
                $table->text('status_notes')->nullable()->after('days_to_ready');
                $table->string('previous_status')->nullable()->after('status_notes');

                // Add indexes
                $table->index(['company_id']);
                $table->index(['created_by']);
                $table->index(['company_id', 'status']);
            });
        }

        // Step 6: Create Mail All Center company if it doesn't exist
        $mailAllCenter = DB::table('companies')->where('slug', 'mail-all-center')->first();
        if (!$mailAllCenter) {
            $mailAllCenterId = DB::table('companies')->insertGetId([
                'name' => 'Mail All Center',
                'slug' => 'mail-all-center',
                'subdomain' => 'mailallcenter',
                'email' => 'info@mailallcenter.com',
                'phone' => '555-MAIL-ALL',
                'address' => '123 Main Street, Anytown, ST 12345',
                'status' => 'active',
                'subscription_plan' => 'premium',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $mailAllCenterId = $mailAllCenter->id;
        }

        // Step 7: Update existing users to be super admin or assign to Mail All Center
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Update specific users to super admin
        $superAdminEmails = ['eldrin.bradecina@gmail.com', 'mailallcenter1@gmail.com'];
        $superAdminUsernames = ['khairo', 'patel'];

        DB::table('users')
            ->whereIn('email', $superAdminEmails)
            ->orWhereIn('username', $superAdminUsernames)
            ->update([
                'is_super_admin' => true,
                'company_id' => null,
                'updated_at' => now()
            ]);

        // Assign remaining users to Mail All Center
        DB::table('users')
            ->whereNull('company_id')
            ->where('is_super_admin', false)
            ->update([
                'company_id' => $mailAllCenterId,
                'updated_at' => now()
            ]);

        // Step 8: Assign all existing packages to Mail All Center and update their status
        $affectedPackages = DB::table('packages')
            ->whereNull('company_id')
            ->update([
                'company_id' => $mailAllCenterId,
                'status' => 'Ready for Pickup', // Set existing packages to Ready for Pickup
                'received_at' => DB::raw('created_at'), // Set received_at to creation date
                'ready_at' => DB::raw('created_at'), // Set ready_at to creation date since they're old
                'updated_at' => now()
            ]);

        // Step 9: Update ALL existing packages to Ready for Pickup status (regardless of company_id)
        DB::table('packages')
            ->where('created_at', '<', now()->subDays(1)) // Packages older than 1 day
            ->update([
                'status' => 'Ready for Pickup',
                'received_at' => DB::raw('COALESCE(received_at, created_at)'), // Set received_at if null
                'ready_at' => DB::raw('COALESCE(ready_at, created_at)'), // Set ready_at if null
                'updated_at' => now()
            ]);

        $totalPackages = DB::table('packages')->count();
        $readyPackages = DB::table('packages')->where('status', 'Ready for Pickup')->count();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "Multi-company setup completed:\n";
        echo "- Mail All Center company created (ID: {$mailAllCenterId})\n";
        echo "- Updated existing users\n";
        echo "- Assigned {$affectedPackages} packages to Mail All Center\n";
        echo "- Updated all existing packages to 'Ready for Pickup' status\n";
        echo "- Total packages: {$totalPackages}, Ready for pickup: {$readyPackages}\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove company_id from packages
        if (Schema::hasColumn('packages', 'company_id')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropForeign(['created_by']);
                $table->dropColumn([
                    'company_id', 'created_by', 'metadata',
                    'received_at', 'ready_at', 'picked_up_at', 'notified_at',
                    'auto_ready', 'days_to_ready', 'status_notes', 'previous_status'
                ]);
            });
        }

        // Remove company_id from users
        if (Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn(['company_id', 'is_super_admin']);
            });
        }

        // Drop tables in reverse order
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('companies');
    }
};
