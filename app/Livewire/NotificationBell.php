<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class NotificationBell extends Component
{
    public int $unreadCount = 0;
    public array $notifications = [];
    public bool $showDropdown = false;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }

    public function loadNotifications()
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        
        // Get unread count
        $this->unreadCount = $user->unreadNotifications()->count();
        
        // Get recent notifications (last 10)
        $this->notifications = $user->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'time_ago' => $notification->created_at->diffForHumans(),
                    'is_unread' => is_null($notification->read_at),
                ];
            })
            ->toArray();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
        
        if ($this->showDropdown) {
            $this->loadNotifications();
        }
    }

    public function markAsRead(string $notificationId)
    {
        if (!Auth::check()) {
            return;
        }

        $notification = Auth::user()->notifications()->find($notificationId);
        
        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        if (!Auth::check()) {
            return;
        }

        Auth::user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
        $this->showDropdown = false;
    }

    public function deleteNotification(string $notificationId)
    {
        if (!Auth::check()) {
            return;
        }

        $notification = Auth::user()->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->delete();
            $this->loadNotifications();
        }
    }

    #[On('notification-sent')]
    public function onNotificationSent()
    {
        $this->loadNotifications();
    }

    public function getNotificationIcon(string $type): string
    {
        return match ($type) {
            'App\Notifications\BatchFullNotification' => 'heroicon-o-user-group',
            'App\Notifications\AdApprovalNotification' => 'heroicon-o-check-circle',
            'App\Notifications\AdRejectionNotification' => 'heroicon-o-x-circle',
            'App\Notifications\TransactionSuccessNotification' => 'heroicon-o-banknotes',
            'App\Notifications\TransactionFailureNotification' => 'heroicon-o-exclamation-triangle',
            'App\Notifications\ChannelApprovalNotification' => 'heroicon-o-megaphone',
            'App\Notifications\ChannelRejectionNotification' => 'heroicon-o-no-symbol',
            'App\Notifications\EscrowReleaseNotification' => 'heroicon-o-lock-open',
            default => 'heroicon-o-bell',
        };
    }

    public function getNotificationColor(string $type): string
    {
        return match ($type) {
            'App\Notifications\AdApprovalNotification',
            'App\Notifications\TransactionSuccessNotification',
            'App\Notifications\ChannelApprovalNotification',
            'App\Notifications\EscrowReleaseNotification' => 'text-green-600',
            'App\Notifications\AdRejectionNotification',
            'App\Notifications\TransactionFailureNotification',
            'App\Notifications\ChannelRejectionNotification' => 'text-red-600',
            'App\Notifications\BatchFullNotification' => 'text-blue-600',
            default => 'text-gray-600',
        };
    }

    public function closeDropdown()
    {
        $this->showDropdown = false;
    }
}