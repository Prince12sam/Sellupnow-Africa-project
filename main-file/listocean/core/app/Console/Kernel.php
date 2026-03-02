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
        // Run every hour to deactivate expired featured ad slots
        $schedule->command('featuredads:expire')->hourly();

        // Run every 30 minutes to mark expired boosts
        $schedule->command('boosts:expire')->everyThirtyMinutes();

        // Run hourly to auto-release overdue escrow orders
        $schedule->command('escrow:auto-release')->hourly();

        // Run daily to expire user memberships past their expire_date
        $schedule->command('memberships:expire')->daily();

        // Regenerate XML sitemap every Sunday at 02:00
        $schedule->command('sitemap:generate')->weekly()->sundays()->at('02:00');
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
