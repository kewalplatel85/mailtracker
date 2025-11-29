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
            // Add workflow status timestamps
            $table->timestamp('received_at')->nullable()->after('status');
            $table->timestamp('ready_at')->nullable()->after('received_at');
            $table->timestamp('picked_up_at')->nullable()->after('ready_at');
            $table->timestamp('notified_at')->nullable()->after('picked_up_at');

            // Add workflow flags
            $table->boolean('auto_ready')->default(true)->after('notified_at');
            $table->integer('days_to_ready')->default(0)->after('auto_ready');
            $table->text('status_notes')->nullable()->after('days_to_ready');

            // Add previous status for tracking transitions
            $table->string('previous_status')->nullable()->after('status_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'received_at',
                'ready_at',
                'picked_up_at',
                'notified_at',
                'auto_ready',
                'days_to_ready',
                'status_notes',
                'previous_status'
            ]);
        });
    }
};
