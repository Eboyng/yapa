<?php

namespace App\Console\Commands;

use App\Models\Batch;
use App\Models\NotificationLog;
use App\Models\User;
use App\Mail\NotificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CleanupTrialBatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batches:cleanup-trials {--dry-run : Show what would be cleaned up without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close expired trial batches that have exceeded their trial period';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting trial batch cleanup...');
        
        $dryRun = $this->option('dry-run');
        
        try {
            // Get expired trial batches
            $expiredBatches = Batch::where('is_trial', true)
                ->where('status', 'active')
                ->where('trial_expires_at', '<', now())
                ->with(['creator', 'members.user'])
                ->get();

            if ($expiredBatches->isEmpty()) {
                $this->info('No expired trial batches found.');
                return self::SUCCESS;
            }

            $this->info("Found {$expiredBatches->count()} expired trial batches.");

            $closedCount = 0;
            
            foreach ($expiredBatches as $batch) {
                if ($dryRun) {
                    $this->line("Would close trial batch: {$batch->name} (ID: {$batch->id}) - Expired: {$batch->trial_expires_at}");
                    continue;
                }

                try {
                    // Close the batch
                    $batch->update([
                        'status' => 'closed',
                        'closed_at' => now(),
                        'closure_reason' => 'Trial period expired',
                    ]);

                    // Notify batch members
                    $this->notifyBatchMembers($batch);

                    // Log the closure
                    NotificationLog::create([
                        'type' => 'batch_trial_expired',
                        'channel' => NotificationLog::CHANNEL_EMAIL,
                        'recipient' => $batch->creator->email ?? 'system',
                        'message' => "Trial batch '{$batch->name}' has been closed due to trial expiration.",
                        'user_id' => $batch->creator_id,
                        'status' => 'sent',
                        'sent_at' => now(),
                        'metadata' => json_encode([
                            'batch_id' => $batch->id,
                            'batch_name' => $batch->name,
                            'expired_at' => $batch->trial_expires_at,
                        ]),
                    ]);

                    $closedCount++;
                    $this->info("Closed trial batch: {$batch->name} (ID: {$batch->id})");
                } catch (\Exception $e) {
                    $this->error("Failed to close batch {$batch->id}: {$e->getMessage()}");
                    Log::error('Failed to close expired trial batch', [
                        'batch_id' => $batch->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if (!$dryRun) {
                $this->info("Successfully closed {$closedCount} expired trial batches.");
                
                // Notify admins about the cleanup
                $this->notifyAdmins($closedCount, $expiredBatches->count());
            }

            Log::info('Trial batch cleanup completed', [
                'total_expired' => $expiredBatches->count(),
                'closed_count' => $closedCount,
                'dry_run' => $dryRun,
            ]);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Trial batch cleanup failed: {$e->getMessage()}");
            Log::error('Trial batch cleanup failed', [
                'error' => $e->getMessage(),
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Notify batch members about trial expiration.
     */
    private function notifyBatchMembers(Batch $batch): void
    {
        try {
            $members = $batch->members()->with('user')->get();
            $message = "Your trial batch '{$batch->name}' has expired and has been closed. Thank you for trying our service! You can create a new batch anytime.";

            foreach ($members as $member) {
                $user = $member->user;
                
                if ($user && $user->email) {
                    Mail::to($user->email)->queue(
                        new NotificationMail(
                            'Trial Batch Expired',
                            $message,
                            $user
                        )
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify batch members about trial expiration', [
                'batch_id' => $batch->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notify admins about the cleanup results.
     */
    private function notifyAdmins(int $closedCount, int $totalExpired): void
    {
        try {
            $admins = User::where('is_admin', true)->get();
            $message = "Trial batch cleanup completed.\n\nClosed: {$closedCount} batches\nTotal expired: {$totalExpired} batches\nTime: " . now()->format('Y-m-d H:i:s');

            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(
                    new NotificationMail(
                        'Trial Batch Cleanup Report',
                        $message,
                        $admin
                    )
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins about trial cleanup', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}