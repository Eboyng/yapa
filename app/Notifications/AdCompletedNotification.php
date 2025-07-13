<?php

namespace App\Notifications;

use App\Models\ChannelAdApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdCompletedNotification extends Notification implements ShouldQueue
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
            ->subject('Your Ad Campaign Has Been Completed!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your ad campaign has been successfully completed.')
            ->line('Channel: ' . $this->application->channelAd->channel_name)
            ->line('Amount: ₦' . number_format($this->application->amount, 2))
            ->line('Start Date: ' . $this->application->start_date->format('M d, Y'))
            ->line('End Date: ' . $this->application->end_date->format('M d, Y'))
            ->line('The campaign has ended and the payment has been released to the channel owner.')
            ->action('View Campaign Details', url('/dashboard/my-ads'))
            ->line('Thank you for using our platform! We hope your campaign was successful.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ad_completed',
            'application_id' => $this->application->id,
            'channel_name' => $this->application->channelAd->channel_name,
            'amount' => $this->application->amount,
            'start_date' => $this->application->start_date->toDateString(),
            'end_date' => $this->application->end_date->toDateString(),
            'message' => 'Your ad campaign for ' . $this->application->channelAd->channel_name . ' has been completed!',
        ];
    }
}