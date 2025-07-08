<!-- Referrals Settings Tab Content -->
<div id="referrals-content" class="tab-content bg-white rounded-2xl p-4 sm:p-6 lg:p-8 border border-gray-100 hidden">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Referrals Settings</h2>
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span>Manage your referral campaigns</span>
        </div>
    </div>

    <!-- Shared Batches -->
    <div class="space-y-6">
        @forelse($sharedBatches as $batch)
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <!-- Batch Info -->
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $batch->name }}</h3>
                        @if($batch->reward_status === 'claimed')
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Claimed</span>
                        @elseif($batch->reward_status === 'available')
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Available</span>
                        @else
                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">In Progress</span>
                        @endif
                    </div>
                    <p class="text-gray-600 text-sm mb-3">{{ $batch->description }}</p>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h6a2 2 0 012 2v4M5 9v10a2 2 0 002 2h10a2 2 0 002-2V9M5 9h14"></path>
                            </svg>
                            <span>Ends: {{ $batch->end_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span>Reward: ₦{{ number_format($batch->reward_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Progress Section -->
                <div class="lg:w-80">
                    <div class="mb-3">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Progress to Reward</span>
                            <span class="font-medium text-gray-900">{{ $batch->current_shares }}/{{ $batch->required_shares }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-orange-500 to-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ min(($batch->current_shares / $batch->required_shares) * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Platform Stats -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-white rounded-lg p-2 text-center">
                            <div class="font-medium text-green-600">{{ $batch->whatsapp_shares }}</div>
                            <div class="text-gray-500">WhatsApp</div>
                        </div>
                        <div class="bg-white rounded-lg p-2 text-center">
                            <div class="font-medium text-blue-600">{{ $batch->facebook_shares }}</div>
                            <div class="text-gray-500">Facebook</div>
                        </div>
                        <div class="bg-white rounded-lg p-2 text-center">
                            <div class="font-medium text-blue-400">{{ $batch->twitter_shares }}</div>
                            <div class="text-gray-500">Twitter</div>
                        </div>
                        <div class="bg-white rounded-lg p-2 text-center">
                            <div class="font-medium text-gray-600">{{ $batch->copy_shares }}</div>
                            <div class="text-gray-500">Copy Link</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div class="text-center">
                        <div class="font-semibold text-gray-900">{{ $batch->total_shares }}</div>
                        <div class="text-gray-500">Total Shares</div>
                    </div>
                    <div class="text-center">
                        <div class="font-semibold text-gray-900">{{ $batch->new_members }}</div>
                        <div class="text-gray-500">New Members</div>
                    </div>
                    <div class="text-center">
                        <div class="font-semibold text-orange-600">₦{{ number_format($batch->potential_reward, 2) }}</div>
                        <div class="text-gray-500">Potential Reward</div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Shared Batches</h3>
            <p class="text-gray-500">You haven't shared any batches yet. Start sharing to earn rewards!</p>
        </div>
        @endforelse
    </div>
</div>