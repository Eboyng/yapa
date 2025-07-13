<?php

namespace App\Notifications;

use App\Models\ChannelAdApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ChannelAdApplication $application;

    /**
     * Create a new notification instance.
     */
    public function __construct(ChannelAdApplication $application)
    {
        $this->application = $application;
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
        return (new MailMessage)
            ->subject('Your Ad Booking Has Been Approved!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! Your ad booking has been approved.')
            ->line('Channel: ' . $this->application->channelAd->channel_name)
            ->line('Amount: ₦' . number_format($this->application->amount, 2))
            ->line('Start Date: ' . $this->application->start_date->format('M d, Y'))
            ->line('End Date: ' . $this->application->end_date->format('M d, Y'))
            ->line('Your payment has been processed and the funds are now held in escrow.')
            ->action('View My Bookings', url('/dashboard/my-ads'))
            ->line('Your ad will be published according to the scheduled dates.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ad_approved',
            'application_id' => $this->application->id,
            'channel_name' => $this->application->channelAd->channel_name,
            'amount' => $this->application->amount,
            'start_date' => $this->application->start_date->toDateString(),
            'end_date' => $this->application->end_date->toDateString(),
            'message' => 'Your ad booking for ' . $this->application->channelAd->channel_name . ' has been approved!',
        ];
    }
}