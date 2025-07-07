<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    System Health
                </h3>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Last checked: {{ now()->format('H:i:s') }}
                </div>
            </div>
            
            @php
                $healthMetrics = $this->getHealthMetrics();
                $systemStats = $this->getSystemStats();
            @endphp
            
            <!-- Health Status Grid -->
            <div class="grid grid-cols-2 gap-3">
                @foreach($healthMetrics as $service => $metric)
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-shrink-0">
                            @php
                                $iconColors = [
                                    'success' => 'text-green-600 dark:text-green-400',
                                    'warning' => 'text-yellow-600 dark:text-yellow-400',
                                    'danger' => 'text-red-600 dark:text-red-400',
                                ];
                            @endphp
                            <svg class="w-5 h-5 {{ $iconColors[$metric['color']] ?? 'text-gray-600 dark:text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                @if($metric['icon'] === 'heroicon-o-check-circle')
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                @elseif($metric['icon'] === 'heroicon-o-exclamation-triangle')
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                @else
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                @endif
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">
                                {{ str_replace('_', ' ', $service) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $metric['message'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- System Stats -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                    System Statistics
                </h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Active Users Today:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ number_format($systemStats['active_users_today']) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Active Batches:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ number_format($systemStats['active_batches']) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Pending Notifications:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ number_format($systemStats['pending_notifications']) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Disk Usage:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $systemStats['disk_usage'] }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Overall Status -->
            @php
                $overallHealthy = collect($healthMetrics)->every(fn($metric) => $metric['status'] === 'healthy');
                $hasWarnings = collect($healthMetrics)->contains(fn($metric) => $metric['status'] === 'warning');
            @endphp
            
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        Overall Status:
                    </span>
                    <div class="flex items-center space-x-2">
                        @if($overallHealthy)
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">All Systems Operational</span>
                        @elseif($hasWarnings)
                            <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Some Issues Detected</span>
                        @else
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">System Issues</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>