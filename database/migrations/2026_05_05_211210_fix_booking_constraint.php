<?php
// database/migrations/xxxx_xx_xx_xxxxxx_fix_booking_constraint.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, find what the constraint is actually named
        // Common names Laravel generates:
        // 1. bookings_booking_event_id_booking_date_time_slot_unique
        // 2. unique_appointment_slot
        // 3. bookings_booking_event_id_booking_date_time_slot_unique

        // Get the actual index name from the database
        $indexes = DB::select("
            SELECT INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'bookings'
            AND NON_UNIQUE = 0
            AND COLUMN_NAME IN ('booking_event_id', 'booking_date', 'time_slot')
            GROUP BY INDEX_NAME
            HAVING COUNT(*) >= 2
        ");

        foreach ($indexes as $index) {
            $indexName = $index->INDEX_NAME;

            if (!empty($indexName)) {
                // Drop the existing unique constraint
                Schema::table('bookings', function (Blueprint $table) use ($indexName) {
                    $table->dropUnique($indexName);
                });

                $this->info("Dropped constraint: {$indexName}");
            }
        }

        // Simple approach: Try common names and catch errors
        $possibleNames = [
            'bookings_booking_event_id_booking_date_time_slot_unique',
            'unique_appointment_slot',
            'unique_active_appointment',
        ];

        foreach ($possibleNames as $name) {
            try {
                Schema::table('bookings', function (Blueprint $table) use ($name) {
                    $table->dropUnique($name);
                });
                echo "Dropped: {$name}\n";
            } catch (\Exception $e) {
                // Constraint doesn't exist with this name, continue
            }
        }
    }

    public function down(): void
    {
        // Restore original unique constraint
        Schema::table('bookings', function (Blueprint $table) {
            $table->unique(
                ['booking_event_id', 'booking_date', 'time_slot'],
                'bookings_booking_event_id_booking_date_time_slot_unique'
            );
        });
    }
};
