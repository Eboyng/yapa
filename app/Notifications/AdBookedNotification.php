<?php

namespace App\Notifications;

use App\Models\ChannelAdApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdBookedNotification extends Notification implements ShouldQueue
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
            ->subject('New Ad Booking for Your Channel')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have received a new ad booking for your channel: ' . $this->application->channelAd->channel_name)
            ->line('Advertiser: ' . $this->application->advertiser->name)
            ->line('Amount: ₦' . number_format($this->application->amount, 2))
            ->line('Start Date: ' . $this->application->start_date->format('M d, Y'))
            ->line('End Date: ' . $this->application->end_date->format('M d, Y'))
            ->action('Review Booking', url('/dashboard/incoming-bookings'))
            ->line('Please review and approve or reject this booking request.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ad_booked',
            'application_id' => $this->application->id,
            'channel_name' => $this->application->channelAd->channel_name,
            'advertiser_name' => $this->application->advertiser->name,
            'amount' => $this->application->amount,
            'start_date' => $this->application->start_date->toDateString(),
            'end_date' => $this->application->end_date->toDateString(),
            'message' => 'New ad booking for your channel: ' . $this->application->channelAd->channel_name,
        ];
    }
}