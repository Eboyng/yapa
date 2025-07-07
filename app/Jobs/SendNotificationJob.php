<?php

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public NotificationLog $notificationLog;
    public int $tries = 3;
    public int $backoff = 60; // 1 minute

    /**
     * Create a new job instance.
     */
    public function __construct(NotificationLog $notificationLog)
    {
        $this->notificationLog = $notificationLog;
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        try {
            // Check if notification is still pending
            if (!$this->notificationLog->isPending()) {
                Log::info('Notification already processed', [
                    'notification_id' => $this->notificationLog->id,
                    'status' => $this->notificationLog->status,
                ]);
                return;
            }

            // Process the notification
            $notificationService->processNotification($this->notificationLog);
            
            Log::info('Notification processed successfully', [
                'notification_id' => $this->notificationLog->id,
                'type' => $this->notificationLog->type,
                'user_id' => $this->notificationLog->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Notification job failed', [
                'notification_id' => $this->notificationLog->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);
            
            // If this is the last attempt, mark as failed
            if ($this->attempts() >= $this->tries) {
                $this->notificationLog->markAsFailed(
                    'Job failed after ' . $this->tries . ' attempts: ' . $e->getMessage()
                );
            }
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Notification job permanently failed', [
            'notification_id' => $this->notificationLog->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
        
        $this->notificationLog->markAsFailed(
            'Job permanently failed: ' . $exception->getMessage()
        );
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [60, 300, 900]; // 1 minute, 5 minutes, 15 minutes
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(2);
    }
}