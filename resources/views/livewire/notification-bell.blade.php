<div class="relative" x-data="{ 
    open: @entangle('showDropdown').live,
    isLoading: @entangle('isLoading').live,
    filter: @entangle('filter').live
}" @click.away="open = false">
    <!-- Notification Bell Button -->
    <button 
        wire:click="toggleDropdown" 
        class="relative flex items-center justify-center w-10 h-10 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg transition-all duration-150 hover:bg-gray-100 active:scale-95"
        aria-label="Notifications"
        :class="{ 'animate-pulse': isLoading }"
    >
        <!-- Bell Icon with Animation -->
        <div class="relative">
            <svg class="w-5 h-5" :class="{ 'animate-bounce': {{ $unreadCount > 0 ? 'true' : 'false' }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            
            <!-- Advanced Notification Count Badge -->
            @if($unreadCount > 0)
                <div class="absolute -top-2 -right-2 flex items-center justify-center">
                    <!-- Pulsing Ring Animation -->
                    <div class="absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75 animate-ping"></div>
                    <!-- Main Badge -->
                    <span class="relative inline-flex items-center justify-center min-w-[1.125rem] h-[1.125rem] text-[10px] font-bold leading-none text-white bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-lg border border-white">
                        {{ $unreadCount > 999 ? '999+' : $unreadCount }}
                    </span>
                </div>
            @endif
            
            <!-- Total Count Badge (when no unread) -->
            @if($unreadCount === 0 && $totalCount > 0)
                <div class="absolute -top-1 -right-1 flex items-center justify-center w-3 h-3 bg-gray-400 rounded-full">
                    <span class="text-[8px] font-medium text-white">{{ $totalCount > 99 ? '99+' : $totalCount }}</span>
                </div>
            @endif
        </div>
    </button>

    <!-- Dropdown Menu with Mobile Responsive Design -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-150" 
        x-transition:enter-start="opacity-0 scale-95 translate-y-1" 
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
        x-transition:leave="transition ease-in duration-100" 
        x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
        class="absolute right-0 z-[9999] mt-2 w-72 sm:w-96 max-w-[calc(100vw-1rem)] sm:max-w-[calc(100vw-2rem)] bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none backdrop-blur-sm"
        style="display: none; max-height: calc(100vh - 6rem); transform-origin: top right;"
        x-init="
            $nextTick(() => {
                if (open) {
                    const rect = $el.getBoundingClientRect();
                    const viewportHeight = window.innerHeight;
                    const viewportWidth = window.innerWidth;
                    
                    // Mobile-first responsive positioning
                    if (viewportWidth < 640) {
                        $el.style.right = '0.5rem';
                        $el.style.left = 'auto';
                        $el.style.width = 'calc(100vw - 1rem)';
                    } else if (rect.right > viewportWidth) {
                        $el.style.right = '0px';
                        $el.style.left = 'auto';
                    }
                    
                    // Adjust vertical position if overflowing
                    if (rect.bottom > viewportHeight - 20) {
                        $el.style.bottom = '100%';
                        $el.style.top = 'auto';
                        $el.style.marginBottom = '0.5rem';
                        $el.style.marginTop = '0';
                    }
                }
            })
        "
    >
        <!-- Header with Enhanced Design -->
        <div class="px-4 sm:px-5 py-3 sm:py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Notifications</h3>
                    @if($totalCount > 0)
                        <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $totalCount }} total
                        </span>
                    @endif
                </div>
                <div class="flex items-center space-x-2">
                    @if($unreadCount > 0)
                        <button 
                            wire:click="markAllAsRead" 
                            class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md transition-colors duration-200"
                        >
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="hidden sm:inline">Mark all read</span>
                            <span class="sm:hidden">Read all</span>
                        </button>
                    @endif
                    <button 
                        @click="open = false" 
                        class="p-1 text-gray-400 hover:text-gray-600 rounded-md transition-colors duration-200"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Filter Tabs -->
            <div class="flex space-x-1 mt-2 sm:mt-3">
                <button 
                    wire:click="setFilter('all')" 
                    class="px-2 sm:px-3 py-1 sm:py-1.5 text-xs font-medium rounded-md transition-colors duration-200"
                    :class="filter === 'all' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-white/50'"
                >
                    All
                </button>
                <button 
                    wire:click="setFilter('unread')" 
                    class="px-2 sm:px-3 py-1 sm:py-1.5 text-xs font-medium rounded-md transition-colors duration-200"
                    :class="filter === 'unread' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-white/50'"
                >
                    Unread
                    @if($unreadCount > 0)
                        <span class="ml-1 inline-flex items-center justify-center w-3 sm:w-4 h-3 sm:h-4 text-[8px] sm:text-[10px] font-bold text-white bg-red-500 rounded-full">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </button>
                <button 
                    wire:click="setFilter('read')" 
                    class="px-2 sm:px-3 py-1 sm:py-1.5 text-xs font-medium rounded-md transition-colors duration-200"
                    :class="filter === 'read' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-white/50'"
                >
                    Read
                </button>
            </div>
        </div>

        <!-- Notifications List with Enhanced Styling -->
        <div class="overflow-y-auto" style="max-height: calc(100vh - 12rem);">
            <!-- Loading State -->
            <div x-show="isLoading" class="px-4 sm:px-5 py-6 sm:py-8 text-center">
                <div class="inline-flex items-center space-x-2">
                    <svg class="animate-spin w-4 sm:w-5 h-4 sm:h-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-gray-600">Loading notifications...</span>
                </div>
            </div>
            
            <!-- Notifications List -->
            <div x-show="!isLoading">
                @forelse($notifications as $notification)
                    <div class="px-3 sm:px-5 py-3 sm:py-4 border-b border-gray-100 hover:bg-gray-50 transition-all duration-200 {{ $notification['is_unread'] ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' }} group">
                        <div class="flex items-start space-x-2 sm:space-x-3">
                            <!-- Enhanced Icon with Animation -->
                            <div class="flex-shrink-0">
                                <div class="w-8 sm:w-10 h-8 sm:h-10 rounded-full flex items-center justify-center transition-all duration-200 {{ $this->getNotificationColor($notification['type']) }} {{ $notification['is_unread'] ? 'ring-2 ring-blue-200' : '' }}">
                                    <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @switch($this->getNotificationIcon($notification['type']))
                                            @case('heroicon-o-user-group')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                @break
                                            @case('heroicon-o-check-circle')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @break
                                            @case('heroicon-o-x-circle')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @break
                                            @case('heroicon-o-banknotes')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @break
                                            @case('heroicon-o-exclamation-triangle')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                @break
                                            @default
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        @endswitch
                                    </svg>
                                </div>
                            </div>

                            <!-- Enhanced Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $notification['data']['title'] ?? 'Notification' }}
                                        </p>
                                        <p class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-2 leading-relaxed">
                                            {{ $notification['data']['message'] ?? $notification['data']['body'] ?? 'No message' }}
                                        </p>
                                    </div>
                                    @if($notification['is_unread'])
                                        <div class="flex-shrink-0 ml-2">
                                            <div class="w-2 sm:w-2.5 h-2 sm:h-2.5 bg-blue-600 rounded-full animate-pulse"></div>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Enhanced Action Bar -->
                                <div class="flex items-center justify-between mt-2 sm:mt-3">
                                    <div class="flex items-center space-x-1 sm:space-x-2">
                                        <p class="text-xs text-gray-500 font-medium">
                                            {{ $notification['time_ago'] }}
                                        </p>
                                        @if(isset($notification['data']['type']))
                                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($notification['data']['type']) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        @if($notification['is_unread'])
                                            <button 
                                                wire:click="markAsRead('{{ $notification['id'] }}')"
                                                class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 text-[10px] sm:text-xs font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors duration-200"
                                                title="Mark as read"
                                            >
                                                <svg class="w-2.5 sm:w-3 h-2.5 sm:h-3 mr-0.5 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span class="hidden sm:inline">Read</span>
                                            </button>
                                        @endif
                                        <button 
                                            wire:click="deleteNotification('{{ $notification['id'] }}')"
                                            class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 text-[10px] sm:text-xs font-medium text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 rounded-md transition-colors duration-200"
                                            title="Delete notification"
                                        >
                                            <svg class="w-2.5 sm:w-3 h-2.5 sm:h-3 mr-0.5 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            <span class="hidden sm:inline">Delete</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Enhanced Empty State -->
                    <div class="px-4 sm:px-5 py-8 sm:py-12 text-center">
                        <div class="w-12 sm:w-16 h-12 sm:h-16 mx-auto mb-3 sm:mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 sm:w-8 h-6 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">
                            @if($filter === 'unread')
                                No unread notifications
                            @elseif($filter === 'read')
                                No read notifications
                            @else
                                No notifications yet
                            @endif
                        </h4>
                        <p class="text-xs text-gray-500">
                            @if($filter === 'unread')
                                You're all caught up! No new notifications to review.
                            @elseif($filter === 'read')
                                No previously read notifications found.
                            @else
                                When you receive notifications, they'll appear here.
                            @endif
                        </p>
                    </div>
                @endforelse
                
                <!-- Load More Button -->
                @if($hasMore && count($notifications) > 0)
                    <div class="px-3 sm:px-5 py-2 sm:py-3 border-t border-gray-200">
                        <button 
                            wire:click="loadMore" 
                            class="w-full inline-flex items-center justify-center px-3 sm:px-4 py-1.5 sm:py-2 text-sm font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200"
                        >
                            <svg class="w-3 sm:w-4 h-3 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="hidden sm:inline">Load more notifications</span>
                            <span class="sm:hidden">Load More</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Enhanced Footer -->
        @if(count($notifications) > 0 || $totalCount > 0)
            <div class="px-3 sm:px-5 py-3 sm:py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        @if(route('notifications.index'))
                            <a href="{{ route('notifications.index') }}" class="inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-sm font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200">
                                <svg class="w-3 sm:w-4 h-3 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span class="hidden sm:inline">View all</span>
                                <span class="sm:hidden">All</span>
                            </a>
                        @endif
                        <button 
                            wire:click="refreshNotifications" 
                            class="inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-sm font-medium text-gray-600 hover:text-gray-800 bg-white hover:bg-gray-50 rounded-lg transition-colors duration-200"
                            title="Refresh notifications"
                        >
                            <svg class="w-3 sm:w-4 h-3 sm:h-4 mr-1 sm:mr-2" :class="{ 'animate-spin': isLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="hidden sm:inline">Refresh</span>
                        </button>
                    </div>
                    
                    <!-- Notification Summary -->
                    <div class="text-xs text-gray-500">
                        @if($unreadCount > 0)
                            <span class="font-medium text-blue-600">{{ $unreadCount }} unread</span>
                            @if($totalCount > $unreadCount)
                                <span class="mx-1">•</span>
                                <span>{{ $totalCount - $unreadCount }} read</span>
                            @endif
                        @elseif($totalCount > 0)
                            <span>{{ $totalCount }} total</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced auto-refresh with smart intervals and mobile optimization
        let refreshInterval;
        let isVisible = true;
        let refreshCount = 0;
        let isMobile = window.innerWidth < 768;
        
        // Update mobile detection on resize
        window.addEventListener('resize', function() {
            isMobile = window.innerWidth < 768;
            startRefreshTimer();
        });
        
        function startRefreshTimer() {
            // Clear existing interval
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
            
            // Adjust intervals for mobile to save battery
            const multiplier = isMobile ? 1.5 : 1;
            const baseInterval = 30000 * multiplier; // 30 seconds (45s on mobile)
            const maxInterval = 300000 * multiplier; // 5 minutes (7.5m on mobile)
            const currentInterval = Math.min(baseInterval * Math.pow(1.5, Math.floor(refreshCount / 5)), maxInterval);
            
            refreshInterval = setInterval(() => {
                if (isVisible && window.Livewire) {
                    try {
                        window.Livewire.dispatch('refresh-notifications');
                        refreshCount++;
                    } catch (error) {
                        console.warn('Failed to refresh notifications:', error);
                    }
                }
            }, currentInterval);
        }
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', function() {
            isVisible = !document.hidden;
            if (isVisible) {
                // Reset refresh count when page becomes visible
                refreshCount = 0;
                startRefreshTimer();
                
                // Immediate refresh when page becomes visible
                if (window.Livewire) {
                    setTimeout(() => {
                        try {
                            window.Livewire.dispatch('refresh-notifications');
                        } catch (error) {
                            console.warn('Failed to refresh notifications on visibility change:', error);
                        }
                    }, 1000);
                }
            } else {
                // Clear interval when page is hidden
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
            }
        });
        
        // Handle user interactions with mobile-optimized events
        const events = isMobile ? ['touchstart', 'touchend', 'scroll'] : ['click', 'keydown', 'scroll', 'mousemove'];
        events.forEach(event => {
            document.addEventListener(event, function(e) {
                if (e.target.closest('[wire\\:click*="markAsRead"], [wire\\:click*="deleteNotification"], [wire\\:click*="markAllAsRead"]')) {
                    refreshCount = 0;
                    startRefreshTimer();
                }
            }, { passive: true });
        });
        
        // Start initial timer
        startRefreshTimer();
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    });
</script>
@endpush