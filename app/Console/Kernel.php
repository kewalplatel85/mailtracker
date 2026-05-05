<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('cleanup:temp-images')->daily();

        // Send reminders 1 day before appointment at 9:00 AM
        $schedule->command('bookings:send-reminders --days=1')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/reminders.log'));

        // Also send reminders 2 hours before appointment
        $schedule->command('bookings:send-reminders --days=0 --remind-hours=2')
            ->hourly()
            ->between('06:00', '20:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
