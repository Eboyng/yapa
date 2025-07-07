<?php

namespace App\Mail;

use App\Models\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailMessage;
    public NotificationLog $notificationLog;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $message, NotificationLog $notificationLog)
    {
        $this->emailSubject = $subject;
        $this->emailMessage = $message;
        $this->notificationLog = $notificationLog;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'subject' => $this->emailSubject,
                'message' => $this->emailMessage,
                'notificationLog' => $this->notificationLog,
                'user' => $this->notificationLog->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}