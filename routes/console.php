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
