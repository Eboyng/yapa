<?php

namespace App\Jobs;

use App\Models\Batch;
use App\Models\NotificationLog;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\WhatsAppService;
use App\Mail\NotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBatchFullNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Batch $batch;
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService, WhatsAppService $whatsAppService): void
    {
        try {
            // Check if batch is actually full
            if (!$this->batch->isFull()) {
                Log::info('Batch is not full, skipping notification', [
                    'batch_id' => $this->batch->id,
                    'current_members' => $this->batch->members()->count(),
                    'max_members' => $this->batch->max_members,
                ]);
                return;
            }

            // Get batch members
            $members = $this->batch->members()->with('user')->get();
            
            foreach ($members as $member) {
                $user = $member->user;
                $message = "🎉 Great news! Your batch '{$this->batch->name}' is now full and ready to start! Check your dashboard for updates.";
                
                // Send WhatsApp notification
                if ($user->phone_number) {
                    try {
                        $result = $whatsAppService->send(
                            $user->phone_number,
                            $message
                        );
                        
                        if ($result['success']) {
                            NotificationLog::create([
                                'type' => 'batch_full_whatsapp',
                                'recipient' => $user->phone_number,
                                'message' => $message,
                                'user_id' => $user->id,
                                'status' => 'sent',
                                'sent_at' => now(),
                                'metadata' => json_encode([
                                    'batch_id' => $this->batch->id,
                                    'batch_name' => $this->batch->name,
                                ]),
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('WhatsApp notification failed for batch full', [
                            'user_id' => $user->id,
                            'batch_id' => $this->batch->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
                
                // Send email notification
                if ($user->email) {
                    try {
                        Mail::to($user->email)->queue(
                            new NotificationMail(
                                'Batch Full - Ready to Start!',
                                $message,
                                $user
                            )
                        );
                        
                        NotificationLog::create([
                            'type' => 'batch_full_email',
                            'recipient' => $user->email,
                            'subject' => 'Batch Full - Ready to Start!',
                            'message' => $message,
                            'user_id' => $user->id,
                            'status' => 'queued',
                            'metadata' => json_encode([
                                'batch_id' => $this->batch->id,
                                'batch_name' => $this->batch->name,
                            ]),
                        ]);
                    } catch (\Exception $e) {
                        Log::warning('Email notification failed for batch full', [
                            'user_id' => $user->id,
                            'batch_id' => $this->batch->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            Log::info('Batch full notifications sent', [
                'batch_id' => $this->batch->id,
                'batch_name' => $this->batch->name,
                'members_count' => $members->count(),
                'attempt' => $this->attempts(),
            ]);
        } catch (\Exception $e) {
            Log::error('Batch full notification job failed', [
                'batch_id' => $this->batch->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // If this is the last attempt, notify admins
            if ($this->attempts() >= $this->tries) {
                $this->notifyAdmins($e);
            }

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Batch full notification job permanently failed', [
            'batch_id' => $this->batch->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        $this->notifyAdmins($exception);
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [5, 10, 20]; // 5s, 10s, 20s exponential backoff
    }

    /**
     * Notify admins about batch notification failure.
     */
    private function notifyAdmins(\Throwable $exception): void
    {
        try {
            $admins = User::where('is_admin', true)->get();
            
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(
                    new NotificationMail(
                        'Batch Full Notification Failed',
                        "Batch full notification for batch '{$this->batch->name}' (ID: {$this->batch->id}) failed after {$this->tries} attempts.\n\nError: {$exception->getMessage()}",
                        $admin
                    )
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins about batch notification failure', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(1);
    }
}