<?php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NotificationMail;

class EmailService
{
    /**
     * Send email notification.
     */
    public function send(string $email, string $subject, string $message, NotificationLog $notificationLog): void
    {
        try {
            $notificationLog->update([
                'channel' => NotificationLog::CHANNEL_EMAIL,
                'recipient' => $email,
            ]);

            Mail::to($email)->send(new NotificationMail($subject, $message, $notificationLog));
            
            $notificationLog->markAsSent(null, [
                'email' => $email,
                'subject' => $subject,
                'sent_via' => 'laravel_mail',
            ]);
            
            Log::info('Email notification sent successfully', [
                'notification_id' => $notificationLog->id,
                'email' => $email,
                'subject' => $subject,
            ]);
        } catch (\Exception $e) {
            $notificationLog->markAsFailed('Email send failed: ' . $e->getMessage());
            
            Log::error('Email notification failed', [
                'notification_id' => $notificationLog->id,
                'email' => $email,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Send bulk email notifications.
     */
    public function sendBulk(array $emails): array
    {
        $results = [];
        
        foreach ($emails as $emailData) {
            try {
                $this->send(
                    $emailData['email'],
                    $emailData['subject'],
                    $emailData['message'],
                    $emailData['notification_log']
                );
                $results[] = ['success' => true, 'email' => $emailData['email']];
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'email' => $emailData['email'],
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Validate email address.
     */
    public function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}