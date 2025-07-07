<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Notification Statistics
        </x-slot>

        @php
            $stats = $this->getNotificationStats();
            $failures = $this->getRecentFailures();
        @endphp

        <div class="space-y-6">
            <!-- Today's Overview -->
            <div>
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Today's Performance</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-600 dark:text-blue-400">Total Sent</p>
                                <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{ number_format($stats['today']['total']) }}</p>
                            </div>
                            <div class="text-blue-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-600 dark:text-green-400">Success Rate</p>
                                <p class="text-lg font-semibold text-green-900 dark:text-green-100">{{ $stats['today']['success_rate'] }}%</p>
                            </div>
                            <div class="text-green-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Breakdown -->
            <div>
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Status Breakdown (Today)</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Sent</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($stats['today']['sent']) }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Failed</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($stats['today']['failed']) }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($stats['today']['pending']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Channel Breakdown -->
            <div>
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">By Channel (Today)</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">WhatsApp</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($stats['channels']['whatsapp']) }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">SMS</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($stats['channels']['sms']) }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Email</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($stats['channels']['email']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Failures -->
            @if(count($failures) > 0)
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Recent Failures (24h)</h4>
                    <div class="space-y-2">
                        @foreach($failures as $failure)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600 dark:text-gray-400 truncate flex-1 mr-2">{{ $failure['error'] }}</span>
                                <span class="text-red-600 dark:text-red-400 font-medium">{{ $failure['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>