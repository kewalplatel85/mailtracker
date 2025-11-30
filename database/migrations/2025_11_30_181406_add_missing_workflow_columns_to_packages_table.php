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
        Schema::table('packages', function (Blueprint $table) {
            // Add workflow timestamp columns
            $table->timestamp('received_at')->nullable()->after('metadata');
            $table->timestamp('ready_at')->nullable()->after('received_at');
            $table->timestamp('picked_up_at')->nullable()->after('ready_at');
            $table->timestamp('notified_at')->nullable()->after('picked_up_at');

            // Add workflow control columns
            $table->boolean('auto_ready')->default(false)->after('notified_at');
            $table->integer('days_to_ready')->default(0)->after('auto_ready');
            $table->text('status_notes')->nullable()->after('days_to_ready');
            $table->string('previous_status')->nullable()->after('status_notes');
        });

        // Update existing packages with proper timestamps
        $packages = DB::table('packages')->get();

        foreach ($packages as $package) {
            $updates = [];

            // Set received_at to created_at for existing packages
            $updates['received_at'] = $package->created_at;

            // If status is 'Ready for Pickup', set ready_at
            if ($package->status === 'Ready for Pickup') {
                $updates['ready_at'] = $package->created_at;
            }

            // If status is 'Picked Up', set both ready_at and picked_up_at
            if ($package->status === 'Picked Up') {
                $updates['ready_at'] = $package->created_at;
                $updates['picked_up_at'] = $package->updated_at ?: $package->created_at;
            }

            DB::table('packages')->where('id', $package->id)->update($updates);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'received_at', 'ready_at', 'picked_up_at', 'notified_at',
                'auto_ready', 'days_to_ready', 'status_notes', 'previous_status'
            ]);
        });
    }
};
