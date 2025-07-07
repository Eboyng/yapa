<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Ad Task History</h1>
        <p class="text-gray-600">Track your ad task progress and earnings</p>
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: @entangle('activeTab') }" class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button 
                    @click="activeTab = 'active'"
                    :class="activeTab === 'active' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    Active
                    @if($activeTasks->count() > 0)
                        <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs font-medium">{{ $activeTasks->count() }}</span>
                    @endif
                </button>
                
                <button 
                    @click="activeTab = 'pending'"
                    :class="activeTab === 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    Pending Review
                    @if($pendingTasks->count() > 0)
                        <span class="ml-2 bg-yellow-100 text-yellow-600 py-0.5 px-2 rounded-full text-xs font-medium">{{ $pendingTasks->count() }}</span>
                    @endif
                </button>
                
                <button 
                    @click="activeTab = 'history'"
                    :class="activeTab === 'history' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    History
                </button>
            </nav>
        </div>

        <!-- Active Tasks Tab -->
        <div x-show="activeTab === 'active'" class="mt-6">
            @if($activeTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($activeTasks as $task)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ $task->ad->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($task->ad->description, 100) }}</p>
                                    <div class="flex items-center mt-2 text-xs text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Started: {{ $task->created_at->format('M j, Y g:i A') }}
                                    </div>
                                </div>
                                <div class="ml-4 flex flex-col items-end">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                    <a 
                                        href="{{ route('ads.task', $task) }}"
                                        class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                        Continue →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Tasks</h3>
                    <p class="text-gray-500 mb-4">You don't have any active ad tasks at the moment.</p>
                    <a 
                        href="{{ route('ads.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Browse Available Ads
                    </a>
                </div>
            @endif
        </div>

        <!-- Pending Tasks Tab -->
        <div x-show="activeTab === 'pending'" class="mt-6">
            @if($pendingTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingTasks as $task)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ $task->ad->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($task->ad->description, 100) }}</p>
                                    <div class="flex items-center mt-2 text-xs text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Submitted: {{ $task->submitted_at ? $task->submitted_at->format('M j, Y g:i A') : 'N/A' }}
                                    </div>
                                    @if($task->view_count)
                                        <div class="flex items-center mt-1 text-xs text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            {{ number_format($task->view_count) }} views
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 flex flex-col items-end">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending Review
                                    </span>
                                    @if($task->earnings_amount)
                                        <div class="mt-1 text-sm font-medium text-green-600">
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
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Pending Tasks</h3>
                    <p class="text-gray-500">You don't have any tasks pending review.</p>
                </div>
            @endif
        </div>

        <!-- History Tab -->
        <div x-show="activeTab === 'history'" class="mt-6">
            @if($transactions->count() > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transaction->adTask->ad->title ?? 'N/A' }}
                                            </div>
                                            @if($transaction->description)
                                                <div class="text-sm text-gray-500">
                                                    {{ Str::limit($transaction->description, 50) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                ₦{{ number_format($transaction->amount, 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($transaction->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Approved
                                                </span>
                                            @elseif($transaction->status === 'rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Rejected
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->created_at->format('M j, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($transaction->status === 'rejected' && $transaction->adTask)
                                                <a 
                                                    href="mailto:support@yapa.ng?subject=Ad Task Appeal - Task #{{ $transaction->adTask->id }}&body=I would like to appeal the rejection of my ad task (ID: {{ $transaction->adTask->id }}) for the ad '{{ $transaction->adTask->ad->title }}'. Please review my submission again."
                                                    class="text-blue-600 hover:text-blue-900"
                                                >
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
                    <div class="mt-6">
                        {{ $transactions->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Task History</h3>
                    <p class="text-gray-500 mb-4">You haven't completed any ad tasks yet.</p>
                    <a 
                        href="{{ route('ads.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Start Your First Task
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- User Flagged Warning -->
    @if(auth()->user()->isFlaggedForAds())
        <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <h3 class="font-medium text-red-900 mb-1">Account Flagged</h3>
                    <p class="text-sm text-red-800 mb-2">
                        Your account has been flagged due to multiple rejected submissions. You cannot participate in new ad tasks.
                    </p>
                    @if(auth()->user()->canSubmitAdAppeal())
                        <a 
                            href="mailto:support@yapa.ng?subject=Account Flag Appeal&body=I would like to appeal the flagging of my account. Please review my case."
                            class="inline-flex items-center text-sm font-medium text-red-700 hover:text-red-900"
                        >
                            Submit Appeal
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-1M10 6V5a2 2 0 112 0v1M10 6h4"></path>
                            </svg>
                        </a>
                    @else
                        <p class="text-xs text-red-700">Appeal already submitted. Please wait for admin review.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Summary Stats -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Earnings</p>
                    <p class="text-2xl font-semibold text-gray-900">₦{{ number_format(auth()->user()->getTotalAdEarnings(), 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completed Tasks</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ auth()->user()->approvedAdTasks()->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Rejection Count</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ auth()->user()->ad_rejection_count }}</p>
                </div>
            </div>
        </div>
    </div>
</div>