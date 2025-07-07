<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NotificationLog;
use App\Models\User;
use App\Jobs\SendNotificationJob;
use App\Services\AvatarService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;

class ManageScheduledTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:manage 
                            {action : Action to perform (status, run-avatars, run-notifications, cleanup, test)}
                            {--force : Force execution without confirmation}
                            {--limit=100 : Limit for batch operations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage scheduled tasks and background jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $force = $this->option('force');
        $limit = (int) $this->option('limit');
        
        switch ($action) {
            case 'status':
                $this->showStatus();
                break;
                
            case 'run-avatars':
                $this->runAvatarGeneration($force);
                break;
                
            case 'run-notifications':
                $this->runNotificationProcessing($limit);
                break;
                
            case 'cleanup':
                $this->runCleanup($force);
                break;
                
            case 'test':
                $this->runTests();
                break;
                
            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: status, run-avatars, run-notifications, cleanup, test');
                return 1;
        }
        
        return 0;
    }
    
    private function showStatus()
    {
        $this->info('=== Scheduled Tasks Status ===');
        
        // Notification status
        $pendingNotifications = NotificationLog::where('status', 'pending')->count();
        $failedNotifications = NotificationLog::where('status', 'failed')->count();
        $sentNotifications = NotificationLog::where('status', 'sent')
            ->where('created_at', '>=', now()->subDay())
            ->count();
            
        $this->table(['Metric', 'Count'], [
            ['Pending Notifications', $pendingNotifications],
            ['Failed Notifications', $failedNotifications],
            ['Sent Today', $sentNotifications],
        ]);
        
        // Avatar status
        $usersWithoutAvatars = User::whereNull('avatar')->orWhere('avatar', '')->count();
        $totalUsers = User::count();
        $avatarCoverage = $totalUsers > 0 ? round((($totalUsers - $usersWithoutAvatars) / $totalUsers) * 100, 2) : 0;
        
        $this->table(['Avatar Metric', 'Value'], [
            ['Users without avatars', $usersWithoutAvatars],
            ['Total users', $totalUsers],
            ['Avatar coverage', $avatarCoverage . '%'],
        ]);
        
        // Queue status
        $queueSize = Queue::size();
        $this->info("\nQueue size: {$queueSize} jobs");
    }
    
    private function runAvatarGeneration($force)
    {
        $this->info('Running avatar generation...');
        
        if (!$force && !$this->confirm('This will generate avatars for users. Continue?')) {
            $this->info('Operation cancelled.');
            return;
        }
        
        $exitCode = Artisan::call('avatars:generate');
        $this->info(Artisan::output());
        
        if ($exitCode === 0) {
            $this->info('Avatar generation completed successfully.');
        } else {
            $this->error('Avatar generation failed.');
        }
    }
    
    private function runNotificationProcessing($limit)
    {
        $this->info('Processing pending notifications...');
        
        $pendingNotifications = NotificationLog::where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes(5))
            ->limit($limit)
            ->get();
            
        if ($pendingNotifications->isEmpty()) {
            $this->info('No pending notifications to process.');
            return;
        }
        
        $this->info("Found {$pendingNotifications->count()} pending notifications.");
        
        $progressBar = $this->output->createProgressBar($pendingNotifications->count());
        $progressBar->start();
        
        $dispatched = 0;
        foreach ($pendingNotifications as $notification) {
            try {
                SendNotificationJob::dispatch($notification);
                $dispatched++;
            } catch (\Exception $e) {
                $this->error("\nFailed to dispatch notification {$notification->id}: {$e->getMessage()}");
            }
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        $this->info("Dispatched {$dispatched} notification jobs.");
    }
    
    private function runCleanup($force)
    {
        $this->info('Running cleanup tasks...');
        
        if (!$force && !$this->confirm('This will delete old notification logs. Continue?')) {
            $this->info('Operation cancelled.');
            return;
        }
        
        // Cleanup old notification logs
        $deleted = NotificationLog::where('created_at', '<', now()->subDays(30))
            ->where('status', '!=', 'pending')
            ->delete();
            
        $this->info("Deleted {$deleted} old notification logs.");
        
        // Cleanup failed jobs older than 7 days
        $failedDeleted = NotificationLog::where('created_at', '<', now()->subDays(7))
            ->where('status', 'failed')
            ->delete();
            
        $this->info("Deleted {$failedDeleted} old failed notifications.");
    }
    
    private function runTests()
    {
        $this->info('Running system tests...');
        
        // Test avatar service
        try {
            $avatarService = app(AvatarService::class);
            $testUser = User::first();
            
            if ($testUser) {
                $avatarUrl = $avatarService->generateAvatarUrl($testUser);
                $this->info("✓ Avatar service working. Test URL: {$avatarUrl}");
            } else {
                $this->warn('No users found for avatar testing.');
            }
        } catch (\Exception $e) {
            $this->error("✗ Avatar service failed: {$e->getMessage()}");
        }
        
        // Test notification system
        try {
            $pendingCount = NotificationLog::where('status', 'pending')->count();
            $this->info("✓ Notification system accessible. {$pendingCount} pending notifications.");
        } catch (\Exception $e) {
            $this->error("✗ Notification system failed: {$e->getMessage()}");
        }
        
        // Test queue connection
        try {
            $queueSize = Queue::size();
            $this->info("✓ Queue system working. {$queueSize} jobs in queue.");
        } catch (\Exception $e) {
            $this->error("✗ Queue system failed: {$e->getMessage()}");
        }
        
        $this->info('\nSystem tests completed.');
    }
}
