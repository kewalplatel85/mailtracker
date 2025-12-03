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
        // Make admin and user roles global by setting company_id to null
        // This allows these roles to be used across all companies
        DB::table('roles')
            ->whereIn('slug', ['admin', 'user'])
            ->update(['company_id' => null]);

        // Make the company_id column nullable if it isn't already
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // In case we need to roll back, set company_id back to 1 for admin and user roles
        // This assumes company ID 1 exists in your system
        DB::table('roles')
            ->whereIn('slug', ['admin', 'user'])
            ->update(['company_id' => 1]);

        // Make company_id not nullable again
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable(false)->change();
        });
    }
};
