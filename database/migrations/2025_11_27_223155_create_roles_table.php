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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->json('permissions'); // Array of permission strings
            $table->boolean('is_system_role')->default(false); // Super admin, etc.
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['slug', 'company_id']);
            $table->index(['company_id']);
            $table->index(['is_system_role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
