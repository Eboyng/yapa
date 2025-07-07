<?php

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Services\OtpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;
use App\Models\User;

class SendOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $phoneNumber;
    public string $otp;
    public string $type; // 'whatsapp' or 'sms'
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(string $phoneNumber, string $otp, string $type = 'whatsapp')
    {
        $this->phoneNumber = $phoneNumber;
        $this->otp = $otp;
        $this->type = $type;
        $this->onQueue('otp');
    }

    /**
     * Execute the job.
     */
    public function handle(OtpService $otpService): void
    {
        try {
            if ($this->type === 'whatsapp') {
                $result = $otpService->sendWhatsAppOtp($this->phoneNumber, $this->otp);
            } else {
                $result = $otpService->sendSmsOtp($this->phoneNumber, $this->otp);
            }

            if (!$result['success']) {
                throw new \Exception($result['message'] ?? 'Failed to send OTP');
            }

            Log::info('OTP sent successfully', [
                'phone_number' => $this->phoneNumber,
                'type' => $this->type,
                'attempt' => $this->attempts(),
            ]);
        } catch (\Exception $e) {
            Log::error('OTP sending failed', [
                'phone_number' => $this->phoneNumber,
                'type' => $this->type,
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
        Log::error('OTP job permanently failed', [
            'phone_number' => $this->phoneNumber,
            'type' => $this->type,
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
            'type' => 'otp_' . $this->type,
            'recipient' => $this->phoneNumber,
            'message' => 'OTP: ' . $this->otp,
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Notify admins about the failure
        $this->notifyAdmins($exception);
    }

    /**
     * Notify admins about OTP sending failure.
     */
    private function notifyAdmins(\Throwable $exception): void
    {
        try {
            $admins = User::where('is_admin', true)->get();
            
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(
                    new NotificationMail(
                        'OTP Sending Failed',
                        "OTP sending to {$this->phoneNumber} via {$this->type} failed after {$this->tries} attempts.\n\nError: {$exception->getMessage()}",
                        $admin
                    )
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins about OTP failure', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }
}