<?php

namespace App\Notifications;

use App\Models\Tip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewTipPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Tip $tip
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
        $preview = Str::limit(strip_tags($this->tip->content), 200);
        
        return (new MailMessage)
            ->subject('New Tip: ' . $this->tip->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new tip has been published that you might find interesting.')
            ->line('**' . $this->tip->title . '**')
            ->line($preview)
            ->action('Read Full Tip', route('tips.show', $this->tip->slug))
            ->line('Thank you for being part of our community!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tip_id' => $this->tip->id,
            'tip_title' => $this->tip->title,
            'tip_slug' => $this->tip->slug,
        ];
    }
}