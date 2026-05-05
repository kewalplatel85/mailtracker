<?php
// database/migrations/xxxx_xx_xx_fix_booking_unique_constraint.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old unique constraint
        Schema::table('bookings', function (Blueprint $table) {
            // Find and drop the unique constraint
            // The name might be: bookings_booking_event_id_booking_date_time_slot_unique
            // or: unique_appointment_slot
            try {
                $table->dropUnique('bookings_booking_event_id_booking_date_time_slot_unique');
            } catch (\Exception $e) {
                // Constraint might have a different name
                try {
                    $table->dropUnique('unique_appointment_slot');
                } catch (\Exception $e) {
                    // If both fail, we'll create a new index
                }
            }
        });

        // Add a new unique constraint that EXCLUDES cancelled bookings
        // Note: MySQL partial indexes only work in MySQL 8.0.13+
        DB::statement("
            ALTER TABLE bookings
            ADD UNIQUE INDEX unique_active_appointment
            (booking_event_id, booking_date, time_slot, status)
            WHERE status != 'cancelled'
        ");

        // Alternative for older MySQL: Create a regular index and handle in code
        // DB::statement("
        //     CREATE UNIQUE INDEX unique_active_appointment
        //     ON bookings (booking_event_id, booking_date, time_slot)
        //     WHERE status != 'cancelled'
        // ");
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('unique_active_appointment');

            // Restore original constraint
            $table->unique(
                ['booking_event_id', 'booking_date', 'time_slot'],
                'unique_appointment_slot'
            );
        });
    }
};
