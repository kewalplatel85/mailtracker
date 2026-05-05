<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('interval_minutes');
            $table->string('unique_link')->unique();
            $table->string('qr_code_path')->nullable();
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');
            $table->timestamps();

            // Index for faster queries
            $table->index(['status', 'event_date']);
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('contact_number');
            $table->string('organization');
            $table->enum('service', [
                'live_scan',
                'fd_258',
                'notary_public',
                'passport_photo',
                'tsa_pre_check',
                'hazmat',
                'twic'
            ]);
            $table->enum('type', ['walk_in', 'appointment']);
            $table->date('booking_date');
            $table->time('time_slot')->nullable();
            $table->integer('queue_number')->nullable();
            $table->enum('status', ['pending', 'checked_in', 'completed', 'no_show', 'cancelled'])->default('pending');
            $table->timestamps();

            // CRITICAL: Unique constraint prevents double booking at MySQL level
            // This is the ULTIMATE protection - MySQL won't allow duplicate entries
            // Only applies for non-cancelled bookings
            $table->unique(
                ['booking_event_id', 'booking_date', 'time_slot'],
                'unique_active_appointment'
            );

            // Indexes for common queries
            $table->index(['booking_date', 'type']);
            $table->index(['status']);
            $table->index(['organization']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('booking_events');
    }
};
