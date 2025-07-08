<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="container mx-auto px-4 py-6">
        
        <!-- Summary Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Earnings</p>
                        <p class="text-lg sm:text-2xl font-bold text-green-600">₦{{ number_format(auth()->user()->getTotalAdEarnings(), 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Completed Tasks</p>
                        <p class="text-lg sm:text-2xl font-bold text-blue-600">{{ auth()->user()->approvedAdTasks()->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Rejection Count</p>
                        <p class="text-lg sm:text-2xl font-bold text-yellow-600">{{ auth()->user()->ad_rejection_count }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Flagged Warning -->
        @if(auth()->user()->isFlaggedForAds())
            <div class="mb-8 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-2xl p-6 animate-fade-in">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-red-900 mb-2">Account Flagged</h3>
                        <p class="text-sm text-red-800 mb-4">
                            Your account has been flagged due to multiple rejected submissions. You cannot participate in new ad tasks.
                        </p>
                        @if(auth()->user()->canSubmitAdAppeal())
                            <a 
                                href="mailto:support@yapa.ng?subject=Account Flag Appeal&body=I would like to appeal the flagging of my account. Please review my case."
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-xl hover:bg-red-700 transition-all duration-200 transform hover:scale-105"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.83 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Submit Appeal
                            </a>
                        @else
                            <div class="bg-red-200 rounded-lg p-3">
                                <p class="text-xs text-red-800 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Appeal already submitted. Please wait for admin review.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabs -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="border-b border-gray-200 bg-gray-50">
                <nav class="flex">
                    <button 
                        onclick="setActiveTab('active')"
                        id="activeTabBtn"
                        class="flex-1 py-4 px-6 text-center text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-orange-600 hover:border-orange-300"
                    >
                        <div class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Active
                            @if($activeTasks->count() > 0)
                                <span class="ml-2 bg-orange-100 text-orange-600 py-1 px-2 rounded-full text-xs font-semibold">{{ $activeTasks->count() }}</span>
                            @endif
                        </div>
                    </button>
                    
                    <button 
                        onclick="setActiveTab('pending')"
                        id="pendingTabBtn"
                        class="flex-1 py-4 px-6 text-center text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-orange-600 hover:border-orange-300"
                    >
                        <div class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pending
                            @if($pendingTasks->count() > 0)
                                <span class="ml-2 bg-yellow-100 text-yellow-600 py-1 px-2 rounded-full text-xs font-semibold">{{ $pendingTasks->count() }}</span>
                            @endif
                        </div>
                    </button>
                    
                    <button 
                        onclick="setActiveTab('history')"
                        id="historyTabBtn"
                        class="flex-1 py-4 px-6 text-center text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-orange-600 hover:border-orange-300"
                    >
                        <div class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            History
                        </div>
                    </button>
                </nav>
            </div>

            <!-- Active Tasks Tab -->
            <div id="activeTab" class="p-6 tab-content">
                @if($activeTasks->count() > 0)
                    <div class="space-y-4">
                        @foreach($activeTasks as $task)
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-2xl p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 mb-2">{{ $task->ad->title }}</h3>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($task->ad->description, 100) }}</p>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Started: {{ $task->created_at->format('M j, Y g:i A') }}
                                        </div>
                                    </div>
                                    <div class="ml-4 flex flex-col items-end">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-500 text-white mb-2">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                        <a 
                                            href="{{ route('ads.task', $task) }}"
                                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-purple-500 text-white text-sm font-medium rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 transform hover:scale-105"
                                        >
                                            Continue
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Active Tasks</h3>
                        <p class="text-gray-600 mb-4">You don't have any active ad tasks at the moment.</p>
                        <a 
                            href="{{ route('ads.index') }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-purple-500 text-white text-sm font-medium rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 transform hover:scale-105"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Browse Available Ads
                        </a>
                    </div>
                @endif
            </div>

            <!-- Pending Tasks Tab -->
            <div id="pendingTab" class="p-6 tab-content hidden">
                @if($pendingTasks->count() > 0)
                    <div class="space-y-4">
                        @foreach($pendingTasks as $task)
                            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-200 rounded-2xl p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 mb-2">{{ $task->ad->title }}</h3>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($task->ad->description, 100) }}</p>
                                        <div class="space-y-1">
                                            <div class="flex items-center text-xs text-gray-500">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Submitted: {{ $task->submitted_at ? $task->submitted_at->format('M j, Y g:i A') : 'N/A' }}
                                            </div>
                                            @if($task->view_count)
                                                <div class="flex items-center text-xs text-gray-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    {{ number_format($task->view_count) }} views
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4 flex flex-col items-end">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500 text-white mb-2">
                                            Pending Review
                                        </span>
                                        @if($task->earnings_amount)
                                            <div class="text-sm font-semibold text-green-600">
                                                ₦{{ number_format($task->earnings_amount, 2) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Pending Tasks</h3>
                        <p class="text-gray-600">You don't have any tasks pending review.</p>
                    </div>
                @endif
            </div>

            <!-- History Tab -->
            <div id="historyTab" class="tab-content hidden">
                @if($transactions->count() > 0)
                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ad Campaign</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $transaction->adTask->ad->title ?? 'N/A' }}
                                                </div>
                                                @if($transaction->description)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ Str::limit($transaction->description, 50) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-semibold text-green-600">
                                                    ₦{{ number_format($transaction->amount, 2) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($transaction->status === 'completed')
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Approved
                                                    </span>
                                                @elseif($transaction->status === 'rejected')
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Rejected
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $transaction->created_at->format('M j, Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @if($transaction->status === 'rejected' && $transaction->adTask)
                                                    <a 
                                                        href="mailto:support@yapa.ng?subject=Ad Task Appeal - Task #{{ $transaction->adTask->id }}&body=I would like to appeal the rejection of my ad task (ID: {{ $transaction->adTask->id }}) for the ad '{{ $transaction->adTask->ad->title }}'. Please review my submission again."
                                                        class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-lg hover:bg-orange-200 transition-colors duration-200"
                                                    >
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.83 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Appeal
                                                    </a>
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

                    <!-- Pagination -->
                    @if($transactions->hasPages())
                        <div class="mt-6 px-6 pb-6">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Task History</h3>
                        <p class="text-gray-600 mb-4">You haven't completed any ad tasks yet.</p>
                        <a 
                            href="{{ route('ads.index') }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-purple-500 text-white text-sm font-medium rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 transform hover:scale-105"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Start Your First Task
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
    
    /* Tab transition effects */
    .tab-content {
        transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
    }
    
    .tab-content.hidden {
        opacity: 0;
        transform: translateY(10px);
    }
    
    .tab-content:not(.hidden) {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Custom scrollbar for table */
    .overflow-x-auto::-webkit-scrollbar {
        height: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: linear-gradient(to right, #f97316, #a855f7);
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to right, #ea580c, #9333ea);
    }
</style>

<script>
    // Tab functionality
    let currentActiveTab = 'active';
    
    function setActiveTab(tabName) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active styles from all buttons
        const tabButtons = document.querySelectorAll('[id$="TabBtn"]');
        tabButtons.forEach(button => {
            button.classList.remove('text-orange-600', 'border-orange-500', 'bg-orange-50');
            button.classList.add('text-gray-500');
        });
        
        // Show selected tab content
        const selectedTab = document.getElementById(tabName + 'Tab');
        if (selectedTab) {
            selectedTab.classList.remove('hidden');
        }
        
        // Add active styles to selected button
        const selectedButton = document.getElementById(tabName + 'TabBtn');
        if (selectedButton) {
            selectedButton.classList.remove('text-gray-500');
            selectedButton.classList.add('text-orange-600', 'border-orange-500', 'bg-orange-50');
        }
        
        currentActiveTab = tabName;
    }
    
    // Initialize tabs on page load
    document.addEventListener('DOMContentLoaded', function() {
        setActiveTab('active');
        
        // Add smooth scrolling behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Add intersection observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observe stats cards for scroll animations
        const statsCards = document.querySelectorAll('.grid.grid-cols-1.sm\\:grid-cols-3 > div');
        statsCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = `opacity 0.6s ease-out ${index * 0.1}s, transform 0.6s ease-out ${index * 0.1}s`;
            observer.observe(card);
        });
        
        // Observe task cards for scroll animations
        const taskCards = document.querySelectorAll('.space-y-4 > div');
        taskCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = `opacity 0.6s ease-out ${index * 0.05}s, transform 0.6s ease-out ${index * 0.05}s`;
            observer.observe(card);
        });
    });
    
    // Add click ripple effect for buttons
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('button, a[class*="bg-gradient"]');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (this.disabled) return;
                
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.5);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                `;
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    });
    
    // Add CSS for ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }
        
        .status-badge {
            animation: pulse 2s ease-in-out infinite;
        }
    `;
    document.head.appendChild(style);
    
    // Add status badge animations for pending items
    document.addEventListener('DOMContentLoaded', function() {
        const pendingBadges = document.querySelectorAll('.bg-yellow-500, .bg-yellow-100');
        pendingBadges.forEach(badge => {
            if (badge.textContent.includes('Pending')) {
                badge.classList.add('status-badge');
            }
        });
    });
    
    // Add hover effects for table rows
    document.addEventListener('DOMContentLoaded', function() {
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.01)';
                this.style.transition = 'transform 0.2s ease-out';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    });
</script>
</div>
