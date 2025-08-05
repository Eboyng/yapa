<?php

namespace App\Console\Commands;

use App\Models\AdTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExpireAdTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ads:expire-tasks {--dry-run : Show what would be expired without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically reject ad tasks that have expired (48 hours without screenshot submission)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting ad task expiry check...');
        
        $dryRun = $this->option('dry-run');
        
        try {
            // Get expired ad tasks that are still active
            $expiredTasks = AdTask::where('status', AdTask::STATUS_ACTIVE)
                ->where('started_at', '<=', now()->subHours(AdTask::TASK_EXPIRY_HOURS))
                ->with(['user', 'ad'])
                ->get();

            if ($expiredTasks->isEmpty()) {
                $this->info('No expired ad tasks found.');
                return self::SUCCESS;
            }

            $this->info("Found {$expiredTasks->count()} expired ad tasks.");

            $expiredCount = 0;

            foreach ($expiredTasks as $task) {
                if ($dryRun) {
                    $this->line("[DRY RUN] Would expire task ID: {$task->id} for user: {$task->user->name} (Ad: {$task->ad->title})");
                } else {
                    // Mark task as expired (which automatically rejects it)
                    $task->markAsExpired();
                    
                    $this->line("Expired task ID: {$task->id} for user: {$task->user->name} (Ad: {$task->ad->title})");
                    
                    Log::info('Ad task automatically expired', [
                        'task_id' => $task->id,
                        'user_id' => $task->user_id,
                        'ad_id' => $task->ad_id,
                        'started_at' => $task->started_at,
                        'expired_at' => now(),
                    ]);
                }
                
                $expiredCount++;
            }

            if ($dryRun) {
                $this->info("[DRY RUN] Would expire {$expiredCount} ad tasks.");
            } else {
                $this->info("Successfully expired {$expiredCount} ad tasks.");
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error expiring ad tasks: ' . $e->getMessage());
            Log::error('Failed to expire ad tasks', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return self::FAILURE;
        }
    }
}