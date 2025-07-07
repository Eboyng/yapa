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
        // Trial batch cleanup - Run daily at 2:00 AM
        $schedule->command('batches:cleanup-trials')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Ad campaign expiry - Run every hour
        $schedule->command('ads:expire-campaigns')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        // Weekly engagement report - Run every Sunday at midnight
        $schedule->command('reports:weekly-engagement')
            ->weeklyOn(0, '00:00') // Sunday at midnight
            ->withoutOverlapping()
            ->runInBackground();

        // Queue worker health check - Run every 5 minutes
        $schedule->command('queue:work --stop-when-empty')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        // Clean failed jobs older than 7 days - Run daily at 3:00 AM
        $schedule->command('queue:prune-failed --hours=168') // 7 days = 168 hours
            ->dailyAt('03:00')
            ->withoutOverlapping();

        // Clean old notification logs - Run weekly on Monday at 1:00 AM
        $schedule->call(function () {
            \App\Models\NotificationLog::where('created_at', '<', now()->subDays(30))->delete();
        })->weeklyOn(1, '01:00');

        // Application cache clear - Run daily at 4:00 AM
        $schedule->command('cache:clear')
            ->dailyAt('04:00');

        // Log cleanup - Run weekly on Sunday at 3:00 AM
        $schedule->call(function () {
            $logPath = storage_path('logs');
            $files = glob($logPath . '/laravel-*.log');
            
            foreach ($files as $file) {
                if (filemtime($file) < strtotime('-30 days')) {
                    unlink($file);
                }
            }
        })->weeklyOn(0, '03:00');
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