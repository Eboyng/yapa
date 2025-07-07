<?php

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Models\User;
use App\Mail\NotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $email;
    public string $subject;
    public string $message;
    public ?int $userId;
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(string $email, string $subject, string $message, ?int $userId = null)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->userId = $userId;
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = $this->userId ? User::find($this->userId) : null;
            
            Mail::to($this->email)->send(
                new NotificationMail($this->subject, $this->message, $user)
            );

            // Log successful delivery
            NotificationLog::create([
                'type' => 'email',
                'recipient' => $this->email,
                'subject' => $this->subject,
                'message' => $this->message,
                'user_id' => $this->userId,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Log::info('Email sent successfully', [
                'email' => $this->email,
                'subject' => $this->subject,
                'user_id' => $this->userId,
                'attempt' => $this->attempts(),
            ]);
        } catch (\Exception $e) {
            Log::error('Email sending failed', [
                'email' => $this->email,
                'subject' => $this->subject,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // If this is the last attempt, log failure and notify admins
            if ($this->attempts() >= $this->tries) {
                $this->logFailureAndNotifyAdmins($e);
            }

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Email job permanently failed', [
            'email' => $this->email,
            'subject' => $this->subject,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        $this->logFailureAndNotifyAdmins($exception);
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [5, 10, 20]; // 5s, 10s, 20s exponential backoff
    }

    /**
     * Log failure in NotificationLog and notify admins.
     */
    private function logFailureAndNotifyAdmins(\Throwable $exception): void
    {
        // Create notification log entry
        NotificationLog::create([
            'type' => 'email',
            'recipient' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'user_id' => $this->userId,
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Notify admins about the failure
        $this->notifyAdmins($exception);
    }

    /**
     * Notify admins about email sending failure.
     */
    private function notifyAdmins(\Throwable $exception): void
    {
        try {
            $admins = User::where('is_admin', true)->get();
            
            foreach ($admins as $admin) {
                // Use direct mail sending to avoid infinite loop
                Mail::to($admin->email)->send(
                    new NotificationMail(
                        'Email Delivery Failed',
                        "Email delivery to {$this->email} failed after {$this->tries} attempts.\n\nSubject: {$this->subject}\n\nError: {$exception->getMessage()}",
                        $admin
                    )
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins about email failure', [
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