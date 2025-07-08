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
     * Core send method.
     */
    protected function send(
        User $user,
        string $type,
        string $subject,
        string $message,
        $relatedModel = null,
        ?array $metadata = null
    ): NotificationLog {
        // Create notification log
        $notificationLog = NotificationLog::create([
            'user_id' => $user->id,
            'type' => $type,
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

        // Try WhatsApp first if enabled and user has WhatsApp notifications enabled
        if ($this->shouldSendWhatsApp($user)) {
            try {
                $this->whatsAppService->send(
                    $user->phone,
                    $notificationLog->message,
                    $notificationLog
                );
                return;
            } catch (\Exception $e) {
                \Log::warning('WhatsApp notification failed, falling back to email', [
                    'notification_id' => $notificationLog->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Fallback to email
        try {
            $this->emailService->send(
                $user->email,
                $notificationLog->subject,
                $notificationLog->message,
                $notificationLog
            );
        } catch (\Exception $e) {
            $notificationLog->markAsFailed('Both WhatsApp and email failed: ' . $e->getMessage());
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
}