<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WeeklyTipsDigest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Collection $tips
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Weekly Tips Digest - ' . now()->format('F j, Y'))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Here are the latest tips from this week that you might have missed:');

        foreach ($this->tips as $tip) {
            $preview = Str::limit(strip_tags($tip->content), 150);
            $message->line('**' . $tip->title . '**')
                    ->line($preview)
                    ->line('[Read More](' . route('tips.show', $tip->slug) . ')');
        }

        $message->line('Visit our tips section to discover more insights!')
                ->action('Browse All Tips', route('tips.index'))
                ->line('Thank you for being part of our community!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tips_count' => $this->tips->count(),
            'week_start' => now()->subWeek()->startOfWeek()->toDateString(),
            'week_end' => now()->subWeek()->endOfWeek()->toDateString(),
        ];
    }
}