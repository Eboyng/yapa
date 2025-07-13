<?php

namespace App\Notifications;

use App\Models\ChannelAdApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdRefundedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ChannelAdApplication $application;
    public ?string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(ChannelAdApplication $application, ?string $reason = null)
    {
        $this->application = $application;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Refund Processed for Your Ad Booking')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A refund has been processed for your ad booking.')
            ->line('Channel: ' . $this->application->channelAd->channel_name)
            ->line('Refund Amount: ₦' . number_format($this->application->amount, 2))
            ->line('Original Start Date: ' . $this->application->start_date->format('M d, Y'))
            ->line('Original End Date: ' . $this->application->end_date->format('M d, Y'));

        if ($this->reason) {
            $mailMessage->line('Reason: ' . $this->reason);
        }

        return $mailMessage
            ->line('The refund amount has been credited back to your wallet.')
            ->action('View My Bookings', url('/dashboard/my-ads'))
            ->line('If you have any questions about this refund, please contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ad_refunded',
            'application_id' => $this->application->id,
            'channel_name' => $this->application->channelAd->channel_name,
            'amount' => $this->application->amount,
            'start_date' => $this->application->start_date->toDateString(),
            'end_date' => $this->application->end_date->toDateString(),
            'reason' => $this->reason,
            'message' => 'Refund processed for your ad booking: ' . $this->application->channelAd->channel_name,
        ];
    }
}