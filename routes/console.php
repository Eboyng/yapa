<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendNotificationJob;
use App\Models\NotificationLog;
use App\Models\User;
use App\Services\AvatarService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule avatar generation for new users daily
Schedule::command('avatars:generate')
    ->daily()
    ->at('02:00')
    ->description('Generate avatars for users without avatars');

// Schedule OTP cleanup every hour
Schedule::command('otp:cleanup')
    ->hourly()
    ->description('Clean up expired OTP records');

// Schedule avatar regeneration weekly with different providers
Schedule::command('avatars:generate --provider=dicebear --style=avataaars')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->description('Weekly avatar refresh with DiceBear provider');

// Schedule notification processing
Schedule::call(function () {
    $pendingNotifications = NotificationLog::where('status', 'pending')
        ->where('created_at', '<=', now()->subMinutes(5))
        ->limit(100)
        ->get();
    
    foreach ($pendingNotifications as $notification) {
        SendNotificationJob::dispatch($notification);
    }
})->everyFiveMinutes()
  ->description('Process pending notifications');

// Schedule cleanup of old notification logs
Schedule::call(function () {
    NotificationLog::where('created_at', '<', now()->subDays(30))
        ->where('status', '!=', 'pending')
        ->delete();
})->daily()
  ->at('01:00')
  ->description('Cleanup old notification logs');

// Schedule user avatar validation and regeneration
Schedule::call(function () {
    $avatarService = app(AvatarService::class);
    
    // Find users with broken or missing avatars
    $usersNeedingAvatars = User::where(function ($query) {
        $query->whereNull('avatar')
              ->orWhere('avatar', '')
              ->orWhere('avatar', 'like', '%404%')
              ->orWhere('avatar', 'like', '%error%');
    })->limit(50)->get();
    
    foreach ($usersNeedingAvatars as $user) {
        try {
            $avatarService->updateUserAvatar($user, 'ui-avatars');
        } catch (\Exception $e) {
            \Log::warning("Failed to update avatar for user {$user->id}: {$e->getMessage()}");
        }
    }
})->hourly()
  ->description('Validate and fix user avatars');

// Trial batch cleanup - Run daily at 2:00 AM
Schedule::command('batches:cleanup-trials')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Close expired trial batches');

// Ad campaign expiry - Run every hour
Schedule::command('ads:expire-campaigns')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Close ad campaigns that have exceeded their duration');

// Ad task expiry - Run every hour
Schedule::command('ads:expire-tasks')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Automatically reject ad tasks that have expired (48 hours without screenshot submission)');

// Weekly engagement report - Run every Sunday at midnight
Schedule::command('reports:weekly-engagement')
    ->weeklyOn(0, '00:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Generate weekly engagement reports');

// Weekly user growth report - Run every Monday at 8:00 AM
Schedule::command('reports:user-growth weekly')
    ->weeklyOn(1, '08:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Generate and send weekly user growth reports to admins');

// Monthly user growth report - Run on the 1st of every month at 9:00 AM
Schedule::command('reports:user-growth monthly')
    ->monthlyOn(1, '09:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Generate and send monthly user growth reports to admins');

// Queue worker health check - Run every 5 minutes
Schedule::command('queue:work --stop-when-empty')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Process queued jobs');

// Clean failed jobs older than 7 days - Run daily at 3:00 AM
Schedule::command('queue:prune-failed --hours=168')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->description('Clean failed jobs older than 7 days');

// Clean old notification logs - Run weekly on Monday at 1:00 AM
Schedule::call(function () {
    \App\Models\NotificationLog::where('created_at', '<', now()->subDays(30))->delete();
})->weeklyOn(1, '01:00')
  ->description('Clean old notification logs');

// Application cache clear - Run daily at 4:00 AM
Schedule::command('cache:clear')
    ->dailyAt('04:00')
    ->description('Clear application cache');

// Log cleanup - Run weekly on Sunday at 3:00 AM
Schedule::call(function () {
    $logPath = storage_path('logs');
    $files = glob($logPath . '/laravel-*.log');
    
    foreach ($files as $file) {
        if (filemtime($file) < strtotime('-30 days')) {
            unlink($file);
        }
    }
})->weeklyOn(0, '03:00')
  ->description('Cleanup old log files');
