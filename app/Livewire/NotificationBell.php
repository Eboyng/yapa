<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class NotificationBell extends Component
{
    public int $unreadCount = 0;
    public int $totalCount = 0;
    public array $notifications = [];
    public bool $showDropdown = false;
    public bool $isLoading = false;
    public string $filter = 'all'; // all, unread, read
    
    // Pagination
    public int $perPage = 10;
    public bool $hasMore = false;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }

    /**
     * Load notifications with caching and performance optimization
     */
    public function loadNotifications(bool $refresh = false)
    {
        if (!Auth::check()) {
            $this->resetNotificationData();
            return;
        }

        $this->isLoading = true;
        
        try {
            $user = Auth::user();
            $cacheKey = "user_notifications_{$user->id}_{$this->filter}";
            
            if ($refresh) {
                Cache::forget($cacheKey);
            }
            
            $data = Cache::remember($cacheKey, 300, function () use ($user) {
                return $this->fetchNotificationsFromDatabase($user);
            });
            
            $this->unreadCount = $data['unread_count'];
            $this->totalCount = $data['total_count'];
            $this->notifications = $data['notifications'];
            $this->hasMore = $data['has_more'];
            
        } catch (\Exception $e) {
            \Log::error('Failed to load notifications: ' . $e->getMessage());
            $this->resetNotificationData();
        } finally {
            $this->isLoading = false;
        }
    }
    
    /**
     * Fetch notifications from database
     */
    private function fetchNotificationsFromDatabase($user): array
    {
        // Get counts
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();
        
        // Build query based on filter
        $query = $user->notifications()->latest();
        
        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }
        
        // Get notifications with pagination
        $notifications = $query->take($this->perPage + 1)->get();
        $hasMore = $notifications->count() > $this->perPage;
        
        if ($hasMore) {
            $notifications = $notifications->take($this->perPage);
        }
        
        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
                'time_ago' => $notification->created_at->diffForHumans(),
                'is_unread' => is_null($notification->read_at),
                'priority' => $this->getNotificationPriority($notification->type),
            ];
        })->toArray();
        
        return [
            'unread_count' => $unreadCount,
            'total_count' => $totalCount,
            'notifications' => $formattedNotifications,
            'has_more' => $hasMore,
        ];
    }
    
    /**
     * Reset notification data
     */
    private function resetNotificationData(): void
    {
        $this->unreadCount = 0;
        $this->totalCount = 0;
        $this->notifications = [];
        $this->hasMore = false;
    }

    /**
     * Toggle dropdown with optimized loading
     */
    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
        
        if ($this->showDropdown) {
            $this->loadNotifications();
        }
    }
    
    /**
     * Change notification filter
     */
    public function setFilter(string $filter)
    {
        if (in_array($filter, ['all', 'unread', 'read'])) {
            $this->filter = $filter;
            $this->loadNotifications(true);
        }
    }
    
    /**
     * Load more notifications (pagination)
     */
    public function loadMore()
    {
        if (!$this->hasMore || !Auth::check()) {
            return;
        }
        
        $this->perPage += 10;
        $this->loadNotifications(true);
    }

    /**
     * Mark single notification as read with cache invalidation
     */
    public function markAsRead(string $notificationId)
    {
        if (!Auth::check()) {
            return;
        }

        try {
            $user = Auth::user();
            $notification = $user->notifications()->find($notificationId);
            
            if ($notification && is_null($notification->read_at)) {
                $notification->markAsRead();
                
                // Clear cache for all filters
                $this->clearNotificationCache($user->id);
                $this->loadNotifications(true);
                
                $this->dispatch('notification-read', ['id' => $notificationId]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to mark notification as read: ' . $e->getMessage());
        }
    }

    /**
     * Mark all notifications as read with batch processing
     */
    public function markAllAsRead()
    {
        if (!Auth::check()) {
            return;
        }

        try {
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();
            
            // Clear cache
            $this->clearNotificationCache($user->id);
            $this->loadNotifications(true);
            
            $this->showDropdown = false;
            $this->dispatch('all-notifications-read');
            
        } catch (\Exception $e) {
            \Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
        }
    }

    /**
     * Delete notification with confirmation
     */
    public function deleteNotification(string $notificationId)
    {
        if (!Auth::check()) {
            return;
        }

        try {
            $user = Auth::user();
            $notification = $user->notifications()->find($notificationId);
            
            if ($notification) {
                $notification->delete();
                
                // Clear cache
                $this->clearNotificationCache($user->id);
                $this->loadNotifications(true);
                
                $this->dispatch('notification-deleted', ['id' => $notificationId]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to delete notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear all notifications cache
     */
    private function clearNotificationCache(int $userId): void
    {
        $filters = ['all', 'unread', 'read'];
        foreach ($filters as $filter) {
            Cache::forget("user_notifications_{$userId}_{$filter}");
        }
    }

    #[On('notification-sent')]
    public function onNotificationSent()
    {
        if (Auth::check()) {
            $this->clearNotificationCache(Auth::id());
            $this->loadNotifications(true);
        }
    }
    
    #[On('refresh-notifications')]
    public function refreshNotifications()
    {
        $this->loadNotifications(true);
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