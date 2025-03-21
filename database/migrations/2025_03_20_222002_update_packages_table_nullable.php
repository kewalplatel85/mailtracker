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
            //
            $table->string('customer_name')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
            $table->integer('mailbox_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            //
            $table->string('customer_name')->nullable(false)->change();
            $table->string('phone_number')->nullable(false)->change();
            $table->integer('mailbox_number')->nullable(false)->change();
        });
    }
};
