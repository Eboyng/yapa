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
    
    /**
     * Send voucher via email
     */
    public function sendVoucherEmail($voucher, string $email, ?string $customMessage = null): bool
    {
        try {
            if (!$this->isValidEmail($email)) {
                throw new \Exception('Invalid email address');
            }
            
            $subject = 'Your ' . config('app.name', 'YAPA') . ' Voucher Code';
            $message = $this->buildVoucherEmailMessage($voucher, $customMessage);
            
            // Create notification log
            $notificationLog = NotificationLog::create([
                'type' => 'voucher_email',
                'channel' => NotificationLog::CHANNEL_EMAIL,
                'recipient' => $email,
                'subject' => $subject,
                'message' => $message,
                'status' => NotificationLog::STATUS_PENDING,
                'metadata' => [
                    'voucher_id' => $voucher->id,
                    'voucher_code' => $voucher->code,
                    'custom_message' => $customMessage,
                ],
            ]);
            
            // Send email
            $this->send($email, $subject, $message, $notificationLog);
            
            Log::info('Voucher sent via email', [
                'voucher_id' => $voucher->id,
                'email' => $email,
                'notification_id' => $notificationLog->id,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send voucher via email', [
                'voucher_id' => $voucher->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Build voucher email message
     */
    private function buildVoucherEmailMessage($voucher, ?string $customMessage): string
    {
        $appName = config('app.name', 'YAPA');
        $amount = $voucher->currency === 'NGN' 
            ? '₦' . number_format($voucher->amount, 2)
            : number_format($voucher->amount) . ' ' . $voucher->currency;
        
        $message = "Dear Valued Customer,\n\n";
        
        if ($customMessage) {
            $message .= "{$customMessage}\n\n";
        }
        
        $message .= "🎉 You have received a {$appName} voucher!\n\n";
        $message .= "Voucher Details:\n";
        $message .= "💰 Amount: {$amount}\n";
        $message .= "🔑 Voucher Code: {$voucher->code}\n";
        
        if ($voucher->expires_at) {
            $expiryDate = $voucher->expires_at->format('F j, Y \\a\\t g:i A');
            $message .= "⏰ Expires: {$expiryDate}\n";
        } else {
            $message .= "⏰ Expires: Never\n";
        }
        
        if ($voucher->description) {
            $message .= "📝 Description: {$voucher->description}\n";
        }
        
        $message .= "\n✨ How to redeem:\n";
        $message .= "1. Open your {$appName} app\n";
        $message .= "2. Go to your wallet\n";
        $message .= "3. Select 'Add Money'\n";
        $message .= "4. Choose 'Voucher' as payment method\n";
        $message .= "5. Enter the voucher code: {$voucher->code}\n";
        $message .= "6. Click 'Redeem Voucher'\n";
        
        $message .= "\nThe voucher amount will be instantly added to your Naira wallet.\n\n";
        $message .= "Important: This voucher can only be used once. Please keep this email safe until you redeem it.\n\n";
        $message .= "If you have any questions or need assistance, please contact our support team.\n\n";
        $message .= "Thank you for using {$appName}!\n\n";
        $message .= "Best regards,\n";
        $message .= "The {$appName} Team";
        
        return $message;
    }
}