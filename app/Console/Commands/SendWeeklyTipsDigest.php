<?php

namespace App\Console\Commands;

use App\Models\Tip;
use App\Models\User;
use App\Notifications\WeeklyTipsDigest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendWeeklyTipsDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tips:send-weekly-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly tips digest to all verified users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting weekly tips digest...');

        // Get tips from the last 7 days
        $tips = Tip::published()
            ->where('published_at', '>=', now()->subWeek())
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->get();

        if ($tips->isEmpty()) {
            $this->info('No tips published in the last week. Skipping digest.');
            return;
        }

        $this->info("Found {$tips->count()} tips from the last week.");

        // Get all verified users
        $users = User::whereNotNull('email_verified_at')
            ->whereNotNull('email')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No verified users found. Skipping digest.');
            return;
        }

        $this->info("Sending digest to {$users->count()} verified users...");

        // Send notification to all verified users
        Notification::send($users, new WeeklyTipsDigest($tips));

        $this->info('Weekly tips digest sent successfully!');
    }
}