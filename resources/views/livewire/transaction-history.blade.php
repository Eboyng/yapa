<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        
        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Transaction History
                    </h1>
                    <p class="mt-1 text-gray-600 text-sm sm:text-base">View and manage your transaction history</p>
                </div>
                
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    <button onclick="openFilterModal()"
                            class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Filters
                    </button>
                    
                    <button wire:click="exportTransactions"
                            class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-orange-500 to-purple-500 text-white rounded-xl hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl text-sm font-medium transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Skeleton for Summary Cards -->
        <div wire:loading wire:target="search,categoryFilter,typeFilter,statusFilter,dateFrom,dateTo" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
            @for($i = 0; $i < 5; $i++)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-4 sm:p-6 animate-pulse">
                        <div class="flex items-center">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 rounded-full"></div>
                            <div class="ml-3 sm:ml-4 flex-1">
                                <div class="h-3 bg-gray-200 rounded w-3/4 mb-2"></div>
                                <div class="h-5 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Summary Cards -->
        <div wire:loading.remove wire:target="search,categoryFilter,typeFilter,statusFilter,dateFrom,dateTo" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:border-blue-200 transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Credits</p>
                            <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900">{{ number_format($summary['total_credits']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:border-green-200 transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Naira</p>
                            <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900">₦{{ number_format($summary['total_naira'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:border-yellow-200 transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Earnings</p>
                            <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900">₦{{ number_format($summary['total_earnings'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Pending</p>
                            <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900">{{ $summary['pending_transactions'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:border-red-200 transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-red-500 to-red-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Failed</p>
                            <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900">{{ $summary['failed_transactions'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Skeleton for Transactions -->
        <div wire:loading wire:target="search,categoryFilter,typeFilter,statusFilter,dateFrom,dateTo">
            <!-- Desktop Skeleton -->
            <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                @for($i = 0; $i < 8; $i++)
                                    <th class="px-6 py-3">
                                        <div class="h-4 bg-gray-200 rounded animate-pulse"></div>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @for($i = 0; $i < 10; $i++)
                                <tr>
                                    @for($j = 0; $j < 8; $j++)
                                        <td class="px-6 py-4">
                                            <div class="h-4 bg-gray-200 rounded animate-pulse"></div>
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Skeleton -->
            <div class="lg:hidden space-y-4">
                @for($i = 0; $i < 5; $i++)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 animate-pulse">
                            <div class="flex items-center justify-between mb-3">
                                <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                                <div class="h-5 bg-gray-200 rounded w-16"></div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            </div>
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                                <div class="h-4 bg-gray-200 rounded w-20"></div>
                                <div class="h-4 bg-gray-200 rounded w-24"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Transactions -->
        <div wire:loading.remove wire:target="search,categoryFilter,typeFilter,statusFilter,dateFrom,dateTo">
            @if($transactions->count() > 0)
                <!-- Desktop Table -->
                <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-gradient-to-r hover:from-orange-50 hover:to-purple-50 transition-all duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->created_at->format('M d, Y') }}
                                            <div class="text-xs text-gray-500">
                                                {{ $transaction->created_at->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="max-w-xs truncate" title="{{ $transaction->description }}">
                                                {{ $transaction->description }}
                                            </div>
                                            @if($transaction->source)
                                                <div class="text-xs text-gray-500">{{ $transaction->source }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($transaction->type === 'credit') bg-blue-100 text-blue-800
                                                @elseif($transaction->type === 'naira') bg-green-100 text-green-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ $types[$transaction->type] ?? $transaction->type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $categories[$transaction->category] ?? $transaction->category }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <span class="@if($transaction->amount > 0) text-green-600 @else text-red-600 @endif">
                                                @if($transaction->type === 'credit')
                                                    {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount) }} credits
                                                @else
                                                    {{ $transaction->amount > 0 ? '+' : '' }}₦{{ number_format($transaction->amount, 2) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->status_color }}">
                                                {{ $statuses[$transaction->status] ?? $transaction->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                            <div class="max-w-xs truncate" title="{{ $transaction->reference }}">
                                                {{ $transaction->reference }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($transaction->status === 'failed' && $transaction->canRetry())
                                                <button wire:click="retryTransaction({{ $transaction->id }})" 
                                                        class="text-orange-600 hover:text-orange-900 font-medium transition-colors duration-200 transform hover:scale-105">
                                                    Retry
                                                </button>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Cards -->
                <div class="lg:hidden space-y-4">
                    @foreach($transactions as $transaction)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="p-4">
                                <!-- Header -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($transaction->type === 'credit') bg-blue-100 text-blue-800
                                            @elseif($transaction->type === 'naira') bg-green-100 text-green-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $types[$transaction->type] ?? $transaction->type }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $transaction->status_color }}">
                                            {{ $statuses[$transaction->status] ?? $transaction->status }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ $transaction->created_at->format('M d, Y') }}
                                    </span>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">{{ $transaction->description }}</h3>
                                    @if($transaction->source)
                                        <p class="text-xs text-gray-500">{{ $transaction->source }}</p>
                                    @endif
                                </div>

                                <!-- Amount -->
                                <div class="mb-3">
                                    <span class="text-lg font-semibold @if($transaction->amount > 0) text-green-600 @else text-red-600 @endif">
                                        @if($transaction->type === 'credit')
                                            {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount) }} credits
                                        @else
                                            {{ $transaction->amount > 0 ? '+' : '' }}₦{{ number_format($transaction->amount, 2) }}
                                        @endif
                                    </span>
                                </div>

                                <!-- Footer -->
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div>
                                        <p class="text-xs text-gray-500">Category</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $categories[$transaction->category] ?? $transaction->category }}</p>
                                    </div>
                                    
                                    @if($transaction->status === 'failed' && $transaction->canRetry())
                                        <button wire:click="retryTransaction({{ $transaction->id }})" 
                                                class="px-3 py-1.5 bg-gradient-to-r from-orange-500 to-purple-500 text-white rounded-lg hover:from-orange-600 hover:to-purple-600 transition-all duration-200 text-xs font-medium transform hover:scale-105">
                                            Retry
                                        </button>
                                    @endif
                                </div>

                                <!-- Reference (collapsible) -->
                                @if($transaction->reference)
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <p class="text-xs text-gray-500 mb-1">Reference</p>
                                        <p class="text-xs font-mono text-gray-700 break-all">{{ $transaction->reference }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($transactions->hasPages())
                    <div class="mt-6 sm:mt-8">
                        {{ $transactions->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-12 sm:py-16">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 sm:mb-6 bg-gradient-to-r from-orange-100 to-purple-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">No transactions found</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base max-w-md mx-auto">
                        Try adjusting your filters or date range to find more transactions.
                    </p>
                    <button onclick="openFilterModal()"
                            class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-orange-500 to-purple-500 text-white rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl text-sm sm:text-base font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Adjust Filters
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeFilterModal()" aria-hidden="true"></div>
            
            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

            <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Filter Transactions
                    </h3>
                    <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Search -->
                    <div class="sm:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search Transactions
                        </label>
                        <input wire:model.live.debounce.300ms="search" type="text" id="search" 
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200"
                               placeholder="Search by description, reference, or source...">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Category
                        </label>
                        <select wire:model.live="categoryFilter" id="category" 
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                            <option value="">All Categories</option>
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Type
                        </label>
                        <select wire:model.live="typeFilter" id="type" 
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                            <option value="">All Types</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Status
                        </label>
                        <select wire:model.live="statusFilter" id="status" 
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="dateFrom" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            From Date
                        </label>
                        <input wire:model.live="dateFrom" type="date" id="dateFrom" 
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label for="dateTo" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            To Date
                        </label>
                        <input wire:model.live="dateTo" type="date" id="dateTo" 
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                    </div>
                </div>

                <div class="flex space-x-3 mt-8">
                    <button wire:click="clearFilters" 
                            onclick="closeFilterModal()"
                            class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Clear All
                    </button>
                    <button onclick="closeFilterModal()" 
                            class="flex-1 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-purple-500 border border-transparent rounded-xl hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading wire:target="retryTransaction,exportTransactions" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-200">
            <div class="flex items-center">
                <svg class="animate-spin h-5 w-5 text-orange-600 mr-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-900 font-medium">Processing...</span>
            </div>
        </div>
    </div>

    <script>
        function openFilterModal() {
            const modal = document.getElementById('filterModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            const modalContent = modal.querySelector('div[role="dialog"] > div');
            // This small delay ensures the transition is applied after the display property changes
            requestAnimationFrame(() => {
                modalContent.style.transform = 'scale(1)';
                modalContent.style.opacity = '1';
                modalContent.style.transition = 'all 0.2s ease-out';
            });
        }
        
        function closeFilterModal() {
            const modal = document.getElementById('filterModal');
            const modalContent = modal.querySelector('div[role="dialog"] > div');

            modalContent.style.transform = 'scale(0.95)';
            modalContent.style.opacity = '0';
            modalContent.style.transition = 'all 0.15s ease-in';
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 150);
        }
        
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !document.getElementById('filterModal').classList.contains('hidden')) {
                closeFilterModal();
            }
        });

        // Add micro-interactions for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Add click effect to buttons
            document.addEventListener('click', function(e) {
                if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                    const button = e.target.tagName === 'BUTTON' ? e.target : e.target.closest('button');
                    button.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        button.style.transform = '';
                    }, 100);
                }
            });

            // Add hover effects to table rows
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(2px)';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        });
    </script>
</div>