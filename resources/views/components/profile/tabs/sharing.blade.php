@props(['sharedBatches', 'availableBatches'])

<h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
        </svg>
        Batch Sharing
    </h2>
    
    <!-- My Shared Batches -->
    <div class="mb-8">
        <h3 class="font-semibold text-gray-900 mb-4">My Shared Batches</h3>
        <div class="space-y-6">
            @forelse($sharedBatches as $batch)
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <!-- Batch Info -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h4 class="text-lg font-semibold text-gray-900">{{ $batch->name }}</h4>
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
                <h4 class="text-lg font-medium text-gray-900 mb-2">No Shared Batches</h4>
                <p class="text-gray-500">You haven't shared any batches yet. Start sharing to earn rewards!</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Select Batch to Share -->
    <div class="bg-gray-50 rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Batch to Share</h3>
        
        <div class="space-y-3">
            @forelse($availableBatches as $batch)
            <div class="bg-white rounded-lg p-4 border border-gray-200 hover:border-orange-300 transition-colors cursor-pointer" onclick="selectBatch({{ $batch->id }})">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <input type="radio" name="selected_batch" value="{{ $batch->id }}" class="text-orange-500 focus:ring-orange-500">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $batch->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $batch->description }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-orange-600">₦{{ number_format($batch->reward_amount, 2) }}</div>
                        <div class="text-xs text-gray-500">Reward</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-500">No batches available for sharing</p>
            </div>
            @endforelse
        </div>

        @if(count($availableBatches ?? []) > 0)
        <div class="mt-4">
            <button onclick="generateShareUrl()" class="btn-primary px-6 py-3 bg-gradient-to-r from-orange-500 to-purple-600 text-white rounded-xl font-medium hover:from-orange-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transform transition-all duration-200 hover:scale-105">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    <span>Generate Share URL</span>
                </div>
            </button>
        </div>
        @endif
    </div>

    <!-- Generated Share URL -->
    <div id="share-url-section" class="bg-blue-50 border border-blue-200 rounded-xl p-6 hidden">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">Your Share URL</h3>
        
        <div class="bg-white rounded-lg p-4 mb-4">
            <div class="flex items-center space-x-3">
                <input type="text" id="share-url" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" readonly>
                <button onclick="copyShareUrl()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span>Copy</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Share Buttons -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- WhatsApp Share -->
            <button onclick="shareOnWhatsApp()" class="flex items-center justify-center space-x-3 px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-medium">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.787"/>
                </svg>
                <span>Share on WhatsApp</span>
            </button>

            <!-- Facebook Share -->
            <button onclick="shareOnFacebook()" class="flex items-center justify-center space-x-3 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span>Share on Facebook</span>
            </button>
        </div>

        <!-- Share Instructions -->
        <div class="mt-4 p-3 bg-blue-100 rounded-lg">
            <div class="flex items-start space-x-2">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-700">
                    <p class="font-medium mb-1">How to earn rewards:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        <li>Share your unique URL with friends and family</li>
                        <li>When someone joins using your link, you get credit</li>
                        <li>Reach the required number of referrals to claim your reward</li>
                        <li>Track your progress in the Referrals tab</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>