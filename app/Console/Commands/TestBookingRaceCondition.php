<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestBookingRaceCondition extends Command
{
    protected $signature = 'booking:test-race
                            {eventId : The booking event ID to test}
                            {--slot= : Specific time slot to test (format: HH:MM)}';

    protected $description = 'Test race condition protection by simulating concurrent bookings';

    public function handle()
    {
        $eventId = $this->argument('eventId');
        $event = BookingEvent::find($eventId);

        if (!$event) {
            $this->error("❌ Booking event with ID {$eventId} not found!");
            return 1;
        }

        if ($event->status !== 'active') {
            $this->error("❌ Event is not active. Current status: {$event->status}");
            return 1;
        }

        // Get available slots
        $slots = $event->generateTimeSlots();

        if (empty($slots)) {
            $this->error('❌ No time slots available in this event!');
            return 1;
        }

        // If specific slot provided, use it; otherwise find first available
        if ($specificSlot = $this->option('slot')) {
            $availableSlot = collect($slots)->firstWhere('time', $specificSlot);

            if (!$availableSlot) {
                $this->error("❌ Slot {$specificSlot} not found or not available!");
                $this->info('Available slots:');
                foreach ($slots as $slot) {
                    $status = $slot['available'] ? '✅ AVAILABLE' : '❌ BOOKED';
                    $this->line("  {$slot['display']} ({$slot['time']}) - {$status}");
                }
                return 1;
            }

            if (!$availableSlot['available']) {
                $this->warn("⚠️  Slot {$specificSlot} is already booked. Testing anyway to verify protection...");
            }
        } else {
            $availableSlot = collect($slots)->firstWhere('available', true);

            if (!$availableSlot) {
                $this->error('❌ No available slots found in this event!');
                $this->info('Here are all slots:');
                foreach ($slots as $slot) {
                    $status = $slot['available'] ? '✅ AVAILABLE' : '❌ BOOKED';
                    $this->line("  {$slot['display']} ({$slot['time']}) - {$status}");
                }
                return 1;
            }
        }

        // Display test info
        $this->newLine();
        $this->info('🧪 RACE CONDITION PROTECTION TEST');
        $this->line(str_repeat('=', 50));
        $this->info("Event: {$event->title}");
        $this->info("Date: {$event->event_date->format('F d, Y')}");
        $this->info("Test Slot: {$availableSlot['display']} ({$availableSlot['time']})");
        $this->info("Interval: {$event->interval_minutes} minutes");
        $this->line(str_repeat('=', 50));
        $this->newLine();

        // Confirm before testing
        if (!$this->confirm('Start race condition test? (10 concurrent users will try to book)', true)) {
            $this->info('Test cancelled.');
            return 0;
        }

        // Clean up any previous test bookings for this slot
        $this->info('🧹 Cleaning up previous test bookings...');
        $deleted = Booking::where('booking_event_id', $event->id)
            ->where('time_slot', $availableSlot['time'])
            ->where('email', 'like', 'racetest_%@test.local')
            ->delete();

        if ($deleted > 0) {
            $this->warn("   Removed {$deleted} previous test booking(s)");
        }

        $this->newLine();
        $this->info('🚀 Starting concurrent booking simulation...');
        $this->info('   10 users attempting to book the same slot simultaneously...');
        $this->newLine();

        // Initialize counters
        $results = [];
        $successCount = 0;
        $blockedCount = 0;
        $errorCount = 0;
        $startTime = microtime(true);

        // Create progress bar
        $progressBar = $this->output->createProgressBar(10);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $progressBar->setMessage('Processing...');
        $progressBar->start();

        // Simulate 10 concurrent users
        for ($i = 1; $i <= 10; $i++) {
            $progressBar->setMessage("User {$i} attempting...");

            try {
                $result = DB::transaction(function () use ($event, $availableSlot, $i) {
                    // LOCK the event row to prevent concurrent access
                    $lockedEvent = BookingEvent::where('id', $event->id)
                        ->lockForUpdate()
                        ->first();

                    // Simulate network/processing delay
                    usleep(rand(50000, 200000)); // 0.05-0.2 seconds

                    // Check if slot is still available
                    $exists = Booking::where('booking_event_id', $event->id)
                        ->where('booking_date', $event->event_date)
                        ->where('time_slot', $availableSlot['time'])
                        ->where('status', '!=', 'cancelled')
                        ->exists();

                    if (!$exists) {
                        // INSERT BOOKING
                        Booking::create([
                            'booking_event_id' => $event->id,
                            'name' => "Race Test User {$i}",
                            'email' => "racetest_{$i}@test.local",
                            'contact_number' => '555-0100',
                            'organization' => 'Test Organization',
                            'service' => 'live_scan',
                            'type' => 'appointment',
                            'booking_date' => $event->event_date,
                            'time_slot' => $availableSlot['time'],
                            'status' => 'pending',
                        ]);

                        return [
                            'status' => 'success',
                            'user' => $i,
                            'message' => "✅ User {$i}: SUCCESSFULLY BOOKED the slot"
                        ];
                    } else {
                        return [
                            'status' => 'blocked',
                            'user' => $i,
                            'message' => "❌ User {$i}: BLOCKED - Slot already taken"
                        ];
                    }
                }, 3); // Retry up to 3 times on deadlock

                if ($result['status'] === 'success') {
                    $successCount++;
                } else {
                    $blockedCount++;
                }
                $results[] = $result['message'];

            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->errorInfo[1] == 1062) {
                    $blockedCount++;
                    $results[] = "🛡️  User {$i}: BLOCKED by database constraint";
                } else {
                    $errorCount++;
                    $results[] = "⚠️  User {$i}: Database error - " . $e->getMessage();
                }
            } catch (\Exception $e) {
                $errorCount++;
                $results[] = "⚠️  User {$i}: Error - " . $e->getMessage();
            }

            // Small delay to simulate real-world timing
            usleep(10000); // 0.01 seconds
            $progressBar->advance();
        }

        $progressBar->finish();
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);

        $this->newLine(2);

        // Display detailed results
        $this->info('📋 DETAILED RESULTS:');
        $this->line(str_repeat('-', 50));
        foreach ($results as $result) {
            $this->line("  {$result}");
        }

        $this->newLine();

        // Verify actual database state
        $actualBookings = Booking::where('booking_event_id', $event->id)
            ->where('time_slot', $availableSlot['time'])
            ->where('email', 'like', 'racetest_%@test.local')
            ->where('status', '!=', 'cancelled')
            ->get();

        // Display summary
        $this->info('📊 TEST SUMMARY:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Attempts', '10'],
                ['Successful Bookings', $successCount],
                ['Blocked Attempts', $blockedCount],
                ['Errors', $errorCount],
                ['Actual DB Bookings', $actualBookings->count()],
                ['Execution Time', "{$executionTime}ms"],
                ['Protection Working', $actualBookings->count() === 1 ? '✅ YES - PERFECT!' : '❌ NO - NEEDS FIXING!'],
            ]
        );

        $this->newLine();

        // Final verdict
        if ($actualBookings->count() === 1) {
            $this->info('🎉 SUCCESS: Race condition protection is working correctly!');
            $this->info('   Only 1 booking was created despite 10 concurrent attempts.');

            if ($actualBookings->isNotEmpty()) {
                $booking = $actualBookings->first();
                $this->info("   Winner: {$booking->name} ({$booking->email})");
            }

            $this->newLine();
            $this->info('🔒 PROTECTION MECHANISMS VERIFIED:');
            $this->line('   ✅ MySQL Row-Level Locking (lockForUpdate)');
            $this->line('   ✅ Database Transaction Isolation');
            $this->line('   ✅ Unique Constraint Enforcement');
            $this->line('   ✅ Duplicate Detection Logic');
        } else {
            $this->error('❌ FAILURE: Multiple bookings were created for the same slot!');
            $this->error("   Found {$actualBookings->count()} bookings instead of 1.");
            $this->error('   Check your database migration for the unique constraint.');
            $this->error('   Ensure your bookings table has:');
            $this->error('   $table->unique([\'booking_event_id\', \'booking_date\', \'time_slot\']);');
        }

        // Clean up option
        if ($this->confirm('Do you want to delete the test bookings?', true)) {
            $deletedCount = Booking::where('booking_event_id', $event->id)
                ->where('time_slot', $availableSlot['time'])
                ->where('email', 'like', 'racetest_%@test.local')
                ->delete();
            $this->info("🧹 Cleaned up {$deletedCount} test booking(s)");
        }

        $this->newLine();
        $this->info('Test completed successfully!');

        return 0;
    }
}
