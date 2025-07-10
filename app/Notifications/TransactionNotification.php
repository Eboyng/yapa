<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionNotification extends Notification
{
    use Queueable;

    protected Transaction $transaction;
    protected string $action;
    protected string $subject;
    protected string $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $transaction, string $action, string $subject, string $message)
    {
        $this->transaction = $transaction;
        $this->action = $action;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transaction',
            'action' => $this->action,
            'subject' => $this->subject,
            'message' => $this->message,
            'transaction_id' => $this->transaction->id,
            'transaction_reference' => $this->transaction->reference,
            'amount' => $this->transaction->amount,
            'formatted_amount' => '₦' . number_format($this->transaction->amount, 2),
            'transaction_type' => $this->transaction->type,
            'category' => $this->transaction->category,
            'created_at' => $this->transaction->created_at->toISOString(),
            'icon' => $this->getIcon(),
            'color' => $this->getColor(),
        ];
    }

    /**
     * Get the icon for the notification based on action.
     */
    private function getIcon(): string
    {
        return match ($this->action) {
            'credited' => 'heroicon-o-arrow-down-circle',
            'debited' => 'heroicon-o-arrow-up-circle',
            default => 'heroicon-o-banknotes',
        };
    }

    /**
     * Get the color for the notification based on action.
     */
    private function getColor(): string
    {
        return match ($this->action) {
            'credited' => 'success',
            'debited' => 'warning',
            default => 'info',
        };
    }
}