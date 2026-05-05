<?php
// app/Console/Commands/SendAppointmentReminders.php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\TwilioSMSService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminders extends Command
{
    protected $signature = 'bookings:send-reminders
                            {--days=1 : Days before appointment to send reminder}
                            {--hours= : Hours before appointment (overrides days)}
                            {--dry-run : Show who would receive reminders without sending}';

    protected $description = 'Send SMS reminders for upcoming appointments';

    // Fix: Declare the property
    protected $smsService;

    // Fix: Inject in constructor, not handle()
    public function __construct(TwilioSMSService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $daysBefore = (int) $this->option('days');
        $hoursBefore = $this->option('hours');
        $isDryRun = $this->option('dry-run');

        // If hours specified, use hour-based reminders
        if ($hoursBefore !== null) {
            return $this->sendHourBasedReminders((int) $hoursBefore, $isDryRun);
        }

        // Otherwise use day-based reminders
        return $this->sendDayBasedReminders($daysBefore, $isDryRun);
    }

    private function sendHourBasedReminders(int $hoursBefore, bool $isDryRun)
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        $this->info("Looking for appointments within {$hoursBefore} hours...");

        // Get today's appointments
        $appointments = Booking::where('type', 'appointment')
            ->where('booking_date', $today)
            ->where('status', '!=', 'cancelled')
            ->whereNotIn('status', ['completed', 'no_show'])
            ->get()
            ->filter(function ($booking) use ($now, $hoursBefore) {
                $appointmentTime = Carbon::parse($booking->booking_date . ' ' . $booking->time_slot);
                $diffInHours = $now->diffInHours($appointmentTime, false);

                // Appointment is in the future and within X hours
                return $diffInHours > 0 && $diffInHours <= $hoursBefore;
            });

        if ($appointments->isEmpty()) {
            $this->info("No appointments found within {$hoursBefore} hours.");
            return 0;
        }

        return $this->processReminders($appointments, $isDryRun);
    }

    private function sendDayBasedReminders(int $daysBefore, bool $isDryRun)
    {
        $targetDate = Carbon::now()->addDays($daysBefore)->toDateString();

        $this->info("Looking for appointments on {$targetDate}...");

        $appointments = Booking::where('type', 'appointment')
            ->where('booking_date', $targetDate)
            ->where('status', '!=', 'cancelled')
            ->whereNotIn('status', ['completed', 'no_show'])
            ->get();

        if ($appointments->isEmpty()) {
            $this->info("No appointments found for {$targetDate}");
            return 0;
        }

        return $this->processReminders($appointments, $isDryRun);
    }

    private function processReminders($appointments, bool $isDryRun)
    {
        $this->info("Found {$appointments->count()} appointment(s)");

        if ($isDryRun) {
            $this->newLine();
            $this->info("📋 DRY RUN - No messages will be sent");
            $this->line(str_repeat('-', 60));

            foreach ($appointments as $booking) {
                $this->line("");
                $this->line("📅 <fg=green>{$booking->name}</>");
                $this->line("   Date: {$booking->booking_date->format('M d, Y')}");
                $this->line("   Time: " . date('h:i A', strtotime($booking->time_slot)));
                $this->line("   Phone: {$booking->contact_number}");
                $this->line("   Service: " . Booking::getServices()[$booking->service]);
                $this->line("   Status: {$booking->status}");
            }

            $this->newLine();
            return 0;
        }

        // Actually send reminders
        $sent = 0;
        $failed = 0;

        $progressBar = $this->output->createProgressBar($appointments->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $progressBar->setMessage('Sending...');
        $progressBar->start();

        foreach ($appointments as $booking) {
            $progressBar->setMessage("Sending to {$booking->name}...");

            $result = $this->smsService->sendAppointmentReminder($booking);

            if ($result) {
                $sent++;
            } else {
                $failed++;
                $this->warn("\n  ⚠ Failed: {$booking->name} ({$booking->contact_number})");
            }

            // Small delay to avoid Twilio rate limits
            usleep(100000); // 0.1 seconds
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->line(str_repeat('-', 50));
        $this->info("📊 Results:");
        $this->line("   ✅ Sent: {$sent}");
        if ($failed > 0) {
            $this->line("   ❌ Failed: {$failed}");
        }

        Log::info("Reminder results - Sent: {$sent}, Failed: {$failed}");

        return 0;
    }
}
