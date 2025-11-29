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
        Schema::table('packages', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->after('company_id')->constrained('users')->onDelete('set null');
            $table->json('metadata')->nullable()->after('status'); // Additional package data

            $table->index(['company_id']);
            $table->index(['created_by']);
            $table->index(['company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['company_id', 'created_by', 'metadata']);
        });
    }
};
