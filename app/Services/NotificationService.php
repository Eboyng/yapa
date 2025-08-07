<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\User;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\Queue;

class NotificationService
{
    protected WhatsAppService $whatsAppService;
    protected EmailService $emailService;
    protected SettingService $settingService;

    public function __construct(
        WhatsAppService $whatsAppService,
        EmailService $emailService,
        SettingService $settingService
    ) {
        $this->whatsAppService = $whatsAppService;
        $this->emailService = $emailService;
        $this->settingService = $settingService;
    }

    /**
     * Send OTP notification.
     */
    public function sendOtp(User $user, string $otp): NotificationLog
    {
        $message = "Your Yapa verification code is: {$otp}. This code expires in 5 minutes.";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_OTP,
            subject: 'Yapa Verification Code',
            message: $message,
            metadata: ['otp' => $otp]
        );
    }

    /**
     * Send batch full notification.
     */
    public function sendBatchFull(User $user, $batch): NotificationLog
    {
        $message = "Great news! Your batch '{$batch->name}' is now full and ready to start. Check your app for details.";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_BATCH_FULL,
            subject: 'Batch is Full!',
            message: $message,
            relatedModel: $batch
        );
    }

    /**
     * Send ad approval notification.
     */
    public function sendAdApproval(User $user, $ad): NotificationLog
    {
        $message = "Congratulations! Your ad '{$ad->title}' has been approved and is now live. Start earning from views!";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_AD_APPROVAL,
            subject: 'Ad Approved!',
            message: $message,
            relatedModel: $ad
        );
    }

    /**
     * Send ad rejection notification.
     */
    public function sendAdRejection(User $user, $ad, string $reason): NotificationLog
    {
        $message = "Your ad '{$ad->title}' was rejected. Reason: {$reason}. Please review and resubmit.";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_AD_REJECTION,
            subject: 'Ad Rejected',
            message: $message,
            relatedModel: $ad,
            metadata: ['rejection_reason' => $reason]
        );
    }

    /**
     * Send transaction success notification.
     */
    public function sendTransactionSuccess(User $user, $transaction): NotificationLog
    {
        $amount = number_format($transaction->amount, 2);
        $message = "Transaction successful! ₦{$amount} has been processed. Reference: {$transaction->reference}";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_TRANSACTION_SUCCESS,
            subject: 'Transaction Successful',
            message: $message,
            relatedModel: $transaction
        );
    }

    /**
     * Send transaction failure notification.
     */
    public function sendTransactionFailure(User $user, $transaction, string $reason): NotificationLog
    {
        $amount = number_format($transaction->amount, 2);
        $message = "Transaction failed! ₦{$amount} could not be processed. Reason: {$reason}. Reference: {$transaction->reference}";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_TRANSACTION_FAILURE,
            subject: 'Transaction Failed',
            message: $message,
            relatedModel: $transaction,
            metadata: ['failure_reason' => $reason]
        );
    }

    /**
     * Send channel approval notification.
     */
    public function sendChannelApproval(User $user, $channel): NotificationLog
    {
        $message = "Your channel '{$channel->name}' has been approved! You can now start receiving ad applications.";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_CHANNEL_APPROVAL,
            subject: 'Channel Approved!',
            message: $message,
            relatedModel: $channel
        );
    }

    /**
     * Send channel rejection notification.
     */
    public function sendChannelRejection(User $user, $channel, string $reason): NotificationLog
    {
        $message = "Your channel '{$channel->name}' was rejected. Reason: {$reason}. Please review and resubmit.";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_CHANNEL_REJECTION,
            subject: 'Channel Rejected',
            message: $message,
            relatedModel: $channel,
            metadata: ['rejection_reason' => $reason]
        );
    }

    /**
     * Send escrow release notification.
     */
    public function sendEscrowRelease(User $user, $transaction): NotificationLog
    {
        $amount = number_format($transaction->amount, 2);
        $message = "Escrow funds released! ₦{$amount} has been transferred to your wallet. Reference: {$transaction->reference}";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_ESCROW_RELEASE,
            subject: 'Escrow Released',
            message: $message,
            relatedModel: $transaction
        );
    }

    /**
     * Send general notification.
     */
    public function sendGeneral(User $user, string $subject, string $message, ?array $metadata = null): NotificationLog
    {
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_GENERAL,
            subject: $subject,
            message: $message,
            metadata: $metadata
        );
    }

    /**
     * Send referral reward notification.
     */
    public function sendReferralReward(User $user, float $amount, string $description): NotificationLog
    {
        $subject = 'Referral Reward Earned!';
        $message = "You've earned ₦{$amount} as a referral reward! {$description}";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_GENERAL,
            subject: $subject,
            message: $message,
            metadata: ['amount' => $amount, 'description' => $description]
        );
    }

    /**
     * Send batch share reward notification.
     */
    public function sendBatchShareReward(User $user, $batch, float $amount): NotificationLog
    {
        $subject = 'Batch Sharing Reward!';
        $message = "Congratulations! You've earned {$amount} credits for sharing '{$batch->name}' and getting 10 new members to join!";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_GENERAL,
            subject: $subject,
            message: $message,
            relatedModel: $batch,
            metadata: ['amount' => $amount, 'reward_type' => 'batch_share']
        );
    }

    /**
     * Send booking confirmation notification to advertiser
     */
    public function sendBookingConfirmationNotification($booking): NotificationLog
    {
        $message = "Your advertisement booking for '{$booking->channel->name}' has been confirmed. Duration: {$booking->duration_hours} hours. Amount: ₦" . number_format($booking->total_amount) . ". Waiting for channel owner approval.";
        
        return $this->send(
            user: $booking->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Advertisement Booking Confirmed',
            message: $message,
            relatedModel: $booking
        );
    }

    /**
     * Send new booking notification to channel owner
     */
    public function sendNewBookingNotification($booking): NotificationLog
    {
        $message = "New advertisement booking received for your channel '{$booking->channel->name}'. Amount: ₦" . number_format($booking->total_amount) . ". Please review and accept/reject within 48 hours.";
        
        return $this->send(
            user: $booking->channel->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'New Advertisement Booking',
            message: $message,
            relatedModel: $booking
        );
    }

    /**
     * Send booking accepted notification to advertiser
     */
    public function sendBookingAcceptedNotification($booking): NotificationLog
    {
        $message = "Great news! Your advertisement booking for '{$booking->channel->name}' has been accepted. The channel owner will start your ad soon.";
        
        return $this->send(
            user: $booking->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Advertisement Booking Accepted',
            message: $message,
            relatedModel: $booking
        );
    }

    /**
     * Send booking rejected notification to advertiser
     */
    public function sendBookingRejectedNotification($booking): NotificationLog
    {
        $message = "Your advertisement booking for '{$booking->channel->name}' has been rejected. A full refund has been processed to your wallet.";
        
        return $this->send(
            user: $booking->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Advertisement Booking Rejected',
            message: $message,
            relatedModel: $booking
        );
    }

    /**
     * Send booking started notification to advertiser
     */
    public function sendBookingStartedNotification($booking): NotificationLog
    {
        $message = "Your advertisement on '{$booking->channel->name}' has started! Duration: {$booking->duration_hours} hours.";
        
        return $this->send(
            user: $booking->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Advertisement Started',
            message: $message,
            relatedModel: $booking
        );
    }

    /**
     * Send proof submitted notification to admin and advertiser
     */
    public function sendProofSubmittedNotification($booking): NotificationLog
    {
        // Send to advertiser
        $advertiserMessage = "The channel owner has submitted proof of completion for your advertisement on '{$booking->channel->name}'. Our admin team is reviewing it.";
        
        $this->send(
            user: $booking->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Proof of Completion Submitted',
            message: $advertiserMessage,
            relatedModel: $booking
        );

        // Send to admin users
        $adminUsers = \App\Models\User::role('admin')->get();
        foreach ($adminUsers as $admin) {
            $adminMessage = "New proof submission for advertisement booking on '{$booking->channel->name}'. Please review and approve/reject.";
            
            $this->send(
                user: $admin,
                type: NotificationLog::TYPE_GENERAL,
                subject: 'New Proof Submission for Review',
                message: $adminMessage,
                relatedModel: $booking
            );
        }

        return $this->send(
            user: $booking->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Proof of Completion Submitted',
            message: $advertiserMessage,
            relatedModel: $booking
        );
    }

    /**
     * Send proof approved notification to advertiser and channel owner
     */
    public function sendProofApprovedNotification($booking): NotificationLog
    {
        // Send to advertiser
        $advertiserMessage = "Your advertisement on '{$booking->channel->name}' has been completed successfully! The proof has been approved.";
        
        $this->send(
            user: $booking->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Advertisement Completed Successfully',
            message: $advertiserMessage,
            relatedModel: $booking
        );

        // Send to channel owner
        $channelOwnerMessage = "Payment released! Your advertisement for '{$booking->channel->name}' has been approved. ₦" . number_format($booking->total_amount) . " has been transferred to your wallet.";
        
        return $this->send(
            user: $booking->channel->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Payment Released - Advertisement Completed',
            message: $channelOwnerMessage,
            relatedModel: $booking
        );
    }

    /**
     * Send booking cancelled notification
     */
    public function sendBookingCancelledNotification($booking, $reason = 'timeout'): NotificationLog
    {
        $reasonText = $reason === 'timeout' ? 'The channel owner did not respond within 48 hours.' : 'The booking was cancelled.';
        
        // Send to advertiser
        $advertiserMessage = "Your advertisement booking for '{$booking->channel->name}' has been cancelled. {$reasonText} A full refund has been processed.";
        
        $this->send(
            user: $booking->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Advertisement Booking Cancelled',
            message: $advertiserMessage,
            relatedModel: $booking
        );

        // Send to channel owner
        $channelOwnerMessage = "Advertisement booking for '{$booking->channel->name}' has been cancelled. {$reasonText}";
        
        return $this->send(
            user: $booking->channel->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Advertisement Booking Cancelled',
            message: $channelOwnerMessage,
            relatedModel: $booking
        );
    }

    /**
     * Send refund processed notification
     */
    public function sendRefundProcessedNotification($booking, $amount): NotificationLog
    {
        $message = "Refund processed! ₦" . number_format($amount) . " has been refunded to your wallet for the cancelled booking on '{$booking->channel->name}'.";
        
        return $this->send(
            user: $booking->user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Refund Processed',
            message: $message,
            relatedModel: $booking
        );
    }

    /**
     * Send ad task approved notification.
     */
    public function sendAdTaskApprovedNotification(User $user, $adTask, float $earnings): NotificationLog
    {
        $message = "Great news! Your ad task for '{$adTask->ad->title}' has been approved. You've earned ₦" . number_format($earnings, 2) . " which has been added to your earnings wallet.";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Ad Task Approved - Earnings Added!',
            message: $message,
            relatedModel: $adTask,
            metadata: [
                'earnings_amount' => $earnings,
                'view_count' => $adTask->view_count,
                'ad_title' => $adTask->ad->title
            ]
        );
    }

    /**
     * Send ad task rejected notification.
     */
    public function sendAdTaskRejectedNotification(User $user, $adTask, string $reason): NotificationLog
    {
        $message = "Your ad task for '{$adTask->ad->title}' has been rejected. Reason: {$reason}. You can submit an appeal if you believe this was a mistake.";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Ad Task Rejected',
            message: $message,
            relatedModel: $adTask,
            metadata: [
                'rejection_reason' => $reason,
                'view_count' => $adTask->view_count,
                'ad_title' => $adTask->ad->title
            ]
        );
    }

    /**
     * Send ad task submitted notification.
     */
    public function sendAdTaskSubmittedNotification(User $user, $adTask, float $estimatedEarnings): NotificationLog
    {
        $message = "Your ad task for '{$adTask->ad->title}' has been submitted for review. Estimated earnings: ₦" . number_format($estimatedEarnings, 2) . ". You'll be notified once it's reviewed.";
        
        return $this->send(
            user: $user,
            type: NotificationLog::TYPE_GENERAL,
            subject: 'Ad Task Submitted for Review',
            message: $message,
            relatedModel: $adTask,
            metadata: [
                'estimated_earnings' => $estimatedEarnings,
                'view_count' => $adTask->view_count,
                'ad_title' => $adTask->ad->title
            ]
        );
    }

    /**
     * Core send method.
     */
    public function send(
        User $user,
        string $type,
        string $subject,
        string $message,
        $relatedModel = null,
        ?array $metadata = null
    ): NotificationLog {
        // Determine the preferred channel and recipient
        $channel = $this->shouldSendWhatsApp($user) ? NotificationLog::CHANNEL_WHATSAPP : NotificationLog::CHANNEL_EMAIL;
        $recipient = $channel === NotificationLog::CHANNEL_WHATSAPP ? $user->phone : $user->email;
        
        // Create notification log
        $notificationLog = NotificationLog::create([
            'user_id' => $user->id,
            'type' => $type,
            'channel' => $channel,
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message,
            'status' => NotificationLog::STATUS_PENDING,
            'metadata' => $metadata,
            'related_model_type' => $relatedModel ? get_class($relatedModel) : null,
            'related_model_id' => $relatedModel?->id,
        ]);

        // Queue the notification for processing
        Queue::push(new SendNotificationJob($notificationLog));

        return $notificationLog;
    }

    /**
     * Process notification sending.
     */
    public function processNotification(NotificationLog $notificationLog): void
    {
        $user = $notificationLog->user;
        
        if (!$user) {
            $notificationLog->markAsFailed('User not found');
            return;
        }

        // Use the channel specified in the notification log
        if ($notificationLog->channel === NotificationLog::CHANNEL_WHATSAPP) {
            try {
                $this->whatsAppService->send(
                    $notificationLog->recipient,
                    $notificationLog->message,
                    $notificationLog
                );
                return;
            } catch (\Exception $e) {
                \Log::warning('WhatsApp notification failed, falling back to email', [
                    'notification_id' => $notificationLog->id,
                    'error' => $e->getMessage()
                ]);
                
                // Update channel to email for fallback
                $notificationLog->update([
                    'channel' => NotificationLog::CHANNEL_EMAIL,
                    'recipient' => $user->email
                ]);
            }
        }

        // Send via email (either as primary channel or fallback)
        try {
            $this->emailService->send(
                $notificationLog->recipient,
                $notificationLog->subject,
                $notificationLog->message,
                $notificationLog
            );
        } catch (\Exception $e) {
            $notificationLog->markAsFailed('Email notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Check if WhatsApp should be used for this user.
     */
    protected function shouldSendWhatsApp(User $user): bool
    {
        return $this->settingService->get('whatsapp_notifications_enabled', false) &&
               $user->whatsapp_notifications_enabled &&
               !empty($user->phone);
    }

    /**
     * Retry failed notification.
     */
    public function retryNotification(NotificationLog $notificationLog): bool
    {
        if (!$notificationLog->canRetry()) {
            return false;
        }

        $notificationLog->increment('retry_count');
        $notificationLog->update(['status' => NotificationLog::STATUS_PENDING]);
        
        Queue::push(new SendNotificationJob($notificationLog));
        
        return true;
    }

    /**
     * Send voucher via WhatsApp
     */
    public function sendVoucherWhatsApp($voucher, string $phone, ?string $customMessage = null): bool
    {
        try {
            $message = $this->buildVoucherMessage($voucher, $customMessage, 'whatsapp');
            
            // Format phone number
            $formattedPhone = $this->formatPhoneNumber($phone);
            
            // Create notification log
            $notificationLog = NotificationLog::create([
                'type' => 'voucher_whatsapp',
                'channel' => NotificationLog::CHANNEL_WHATSAPP,
                'recipient' => $formattedPhone,
                'subject' => 'Voucher Code',
                'message' => $message,
                'status' => NotificationLog::STATUS_PENDING,
                'metadata' => [
                    'voucher_id' => $voucher->id,
                    'voucher_code' => $voucher->code,
                    'custom_message' => $customMessage,
                ],
            ]);
            
            // Send via WhatsApp service
            $this->whatsAppService->send($formattedPhone, $message, $notificationLog);
            
            \Log::info('Voucher sent via WhatsApp', [
                'voucher_id' => $voucher->id,
                'phone' => $formattedPhone,
                'notification_id' => $notificationLog->id,
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send voucher via WhatsApp', [
                'voucher_id' => $voucher->id,
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Send voucher via SMS
     */
    public function sendVoucherSMS($voucher, string $phone, ?string $customMessage = null): bool
    {
        try {
            $message = $this->buildVoucherMessage($voucher, $customMessage, 'sms');
            
            // Format phone number
            $formattedPhone = $this->formatPhoneNumber($phone);
            
            // Create notification log
            $notificationLog = NotificationLog::create([
                'type' => 'voucher_sms',
                'channel' => 'sms',
                'recipient' => $formattedPhone,
                'subject' => 'Voucher Code',
                'message' => $message,
                'status' => NotificationLog::STATUS_PENDING,
                'metadata' => [
                    'voucher_id' => $voucher->id,
                    'voucher_code' => $voucher->code,
                    'custom_message' => $customMessage,
                ],
            ]);
            
            // Send via SMS (you'll need to implement SMS sending in WhatsAppService or create SMSService)
            $this->whatsAppService->sendSMS($formattedPhone, $message, $notificationLog);
            
            \Log::info('Voucher sent via SMS', [
                'voucher_id' => $voucher->id,
                'phone' => $formattedPhone,
                'notification_id' => $notificationLog->id,
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send voucher via SMS', [
                'voucher_id' => $voucher->id,
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Build voucher message for different channels
     */
    private function buildVoucherMessage($voucher, ?string $customMessage, string $channel): string
    {
        $appName = config('app.name', 'YAPA');
        $amount = $voucher->currency === 'NGN' 
            ? '₦' . number_format($voucher->amount, 2)
            : number_format($voucher->amount) . ' ' . $voucher->currency;
        
        $message = "🎉 {$appName} Voucher\n\n";
        
        if ($customMessage) {
            $message .= "{$customMessage}\n\n";
        }
        
        $message .= "💰 Amount: {$amount}\n";
        $message .= "🔑 Code: {$voucher->code}\n";
        
        if ($voucher->expires_at) {
            $expiryDate = $voucher->expires_at->format('M j, Y');
            $message .= "⏰ Expires: {$expiryDate}\n";
        }
        
        if ($voucher->description) {
            $message .= "📝 {$voucher->description}\n";
        }
        
        $message .= "\n✨ Redeem this voucher in your {$appName} wallet to add funds instantly!";
        
        // Keep SMS shorter due to character limits
        if ($channel === 'sms') {
            $message = "{$appName} Voucher: {$amount} | Code: {$voucher->code}";
            if ($voucher->expires_at) {
                $message .= " | Expires: {$voucher->expires_at->format('M j')}";
            }
            $message .= " | Redeem in your wallet now!";
        }
        
        return $message;
    }
    
    /**
     * Format phone number for international use
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // If it starts with +, keep it
        if (str_starts_with($phone, '+')) {
            return $phone;
        }
        
        // If it starts with 234 (Nigeria country code), add +
        if (str_starts_with($phone, '234')) {
            return '+' . $phone;
        }
        
        // If it starts with 0, replace with +234
        if (str_starts_with($phone, '0')) {
            return '+234' . substr($phone, 1);
        }
        
        // If it's a Nigerian number without country code, add +234
        if (strlen($phone) === 10) {
            return '+234' . $phone;
        }
        
        // Default: assume it needs +234
        return '+234' . $phone;
    }
}