<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing password_resets table with incorrect structure
        Schema::dropIfExists('password_resets');

        // Recreate password_resets table with correct Laravel structure
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the corrected table
        Schema::dropIfExists('password_resets');

        // Recreate the old incorrect structure (for rollback purposes)
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
