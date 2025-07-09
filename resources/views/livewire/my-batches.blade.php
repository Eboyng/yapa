<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
   
        <!-- Filter Tabs -->
        <div class="mb-8">
            <div class="bg-white/80 backdrop-blur-sm p-1.5 rounded-xl shadow-sm border border-gray-200/80 inline-flex space-x-1">
                <button 
                    wire:click="$set('filter', 'all')"
                    class="py-2 px-4 rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none
                           {{ $filter === 'all' ? 'bg-white shadow text-orange-600' : 'text-gray-600 hover:bg-gray-500/5' }}">
                    All Batches
                </button>
                <button 
                    wire:click="$set('filter', 'active')"
                    class="py-2 px-4 rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none
                           {{ $filter === 'active' ? 'bg-white shadow text-orange-600' : 'text-gray-600 hover:bg-gray-500/5' }}">
                    Active
                </button>
                <button 
                    wire:click="$set('filter', 'closed')"
                    class="py-2 px-4 rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none
                           {{ $filter === 'closed' ? 'bg-white shadow text-orange-600' : 'text-gray-600 hover:bg-gray-500/5' }}">
                    Closed
                </button>
            </div>
        </div>

        <!-- Batch Sharing Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                    </svg>
                    Batch Sharing
                </h2>
                <button 
                    wire:click="toggleBatchShareSection"
                    class="px-4 py-2 bg-gradient-to-r from-orange-500 to-purple-500 text-white rounded-lg hover:from-orange-600 hover:to-purple-600 transition-all duration-200 text-sm font-medium">
                    {{ $showBatchShareSection ? 'Hide' : 'Show' }} Sharing Options
                </button>
            </div>

            @if($showBatchShareSection)
                <!-- Share New Batch Section -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Batch to Share</h3>
                    
                    @if($isLoadingBatches)
                        <div class="text-center py-8">
                            <svg class="animate-spin h-8 w-8 text-orange-500 mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-gray-600 mt-2">Loading batches...</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @forelse($openBatches as $batch)
                                <div class="bg-white rounded-lg p-4 border border-gray-200 hover:border-orange-300 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $batch['name'] ?? "Batch #{$batch['id']}" }}</h4>
                                            <p class="text-sm text-gray-600">{{ $batch['description'] ?? 'No description available' }}</p>
                                            <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                <span>{{ $batch['members_count'] ?? 0 }}/{{ $batch['limit'] ?? 0 }} members</span>
                                                @if(isset($batch['location']))
                                                    <span>{{ $batch['location'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <button 
                                            wire:click="generateBatchShareUrl({{ $batch['id'] }})"
                                            wire:loading.attr="disabled"
                                            wire:target="generateBatchShareUrl({{ $batch['id'] }})"
                                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors text-sm font-medium disabled:opacity-50">
                                            <span wire:loading.remove wire:target="generateBatchShareUrl({{ $batch['id'] }})">Generate Link</span>
                                            <span wire:loading wire:target="generateBatchShareUrl({{ $batch['id'] }})">Generating...</span>
                                        </button>
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
                    @endif
                </div>

                <!-- Generated Share URL Section -->
                @if($selectedBatch && $shareUrl)
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-4">Share: {{ $selectedBatch->name ?? "Batch #{$selectedBatch->id}" }}</h3>
                        
                        <div class="bg-white rounded-lg p-4 mb-4">
                            <div class="flex items-center space-x-3">
                                <input type="text" value="{{ $shareUrl }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" readonly>
                                <button 
                                    onclick="navigator.clipboard.writeText('{{ $shareUrl }}'); alert('Link copied to clipboard!')"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium">
                                    Copy
                                </button>
                            </div>
                        </div>

                        <!-- Share Buttons -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <button 
                                wire:click="shareBatch('whatsapp')"
                                class="flex items-center justify-center space-x-2 px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-medium">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785"/>
                                </svg>
                                <span>WhatsApp</span>
                            </button>

                            <button 
                                wire:click="shareBatch('facebook')"
                                class="flex items-center justify-center space-x-2 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span>Facebook</span>
                            </button>

                            <button 
                                wire:click="shareBatch('twitter')"
                                class="flex items-center justify-center space-x-2 px-4 py-3 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors font-medium">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                                <span>Twitter</span>
                            </button>

                            <button 
                                wire:click="shareBatch('copy_link')"
                                class="flex items-center justify-center space-x-2 px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Copy Link</span>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- My Shared Batches -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">My Shared Batches</h3>
                        <button 
                            wire:click="toggleSharedBatchesList"
                            class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                            {{ $showSharedBatchesList ? 'Hide' : 'Show' }} Shared Batches
                        </button>
                    </div>

                    @if($showSharedBatchesList)
                        @if($isLoadingBatches)
                            <div class="text-center py-8">
                                <svg class="animate-spin h-8 w-8 text-orange-500 mx-auto" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-gray-600 mt-2">Loading shared batches...</p>
                            </div>
                        @else
                            <div class="space-y-6">
                                @forelse($sharedBatches as $batchData)
                                    @php $batch = $batchData['batch']; @endphp
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                            <!-- Batch Info -->
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <h4 class="text-lg font-semibold text-gray-900">{{ $batch['name'] ?? "Batch #{$batch['id']}" }}</h4>
                                                    @if($batchData['reward_claimed'])
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Claimed</span>
                                                    @elseif($batchData['can_claim_reward'])
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Available</span>
                                                    @else
                                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">In Progress</span>
                                                    @endif
                                                </div>
                                                <p class="text-gray-600 text-sm mb-3">{{ $batch['description'] ?? 'No description available' }}</p>
                                            </div>

                                            <!-- Progress Section -->
                                            <div class="lg:w-80">
                                                <div class="mb-3">
                                                    <div class="flex justify-between text-sm mb-1">
                                                        <span class="text-gray-600">Progress to Reward</span>
                                                        <span class="font-medium text-gray-900">{{ $batchData['new_members_count'] }}/10</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-gradient-to-r from-orange-500 to-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $batchData['progress_percentage'] }}%"></div>
                                                    </div>
                                                </div>

                                                <!-- Platform Stats -->
                                                <div class="grid grid-cols-2 gap-2 text-xs">
                                                    <div class="bg-white rounded-lg p-2 text-center">
                                                        <div class="font-medium text-green-600">{{ $batchData['platform_stats']['whatsapp'] }}</div>
                                                        <div class="text-gray-500">WhatsApp</div>
                                                    </div>
                                                    <div class="bg-white rounded-lg p-2 text-center">
                                                        <div class="font-medium text-blue-600">{{ $batchData['platform_stats']['facebook'] }}</div>
                                                        <div class="text-gray-500">Facebook</div>
                                                    </div>
                                                    <div class="bg-white rounded-lg p-2 text-center">
                                                        <div class="font-medium text-blue-400">{{ $batchData['platform_stats']['twitter'] }}</div>
                                                        <div class="text-gray-500">Twitter</div>
                                                    </div>
                                                    <div class="bg-white rounded-lg p-2 text-center">
                                                        <div class="font-medium text-gray-600">{{ $batchData['platform_stats']['copy_link'] }}</div>
                                                        <div class="text-gray-500">Copy Link</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Summary Stats -->
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                                <div class="text-center">
                                                    <div class="font-semibold text-gray-900">{{ $batchData['total_shares'] }}</div>
                                                    <div class="text-gray-500">Total Shares</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="font-semibold text-gray-900">{{ $batchData['new_members_count'] }}</div>
                                                    <div class="text-gray-500">New Members</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="font-semibold text-orange-600">₦{{ number_format($batchData['new_members_count'] * 100, 2) }}</div>
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
                        @endif
                    @endif
                </div>
            @endif
        </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 animate-fade-in">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Batches Grid -->
    @if($batches->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($batches as $batch)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">
                    <!-- Batch Header & Details -->
                    <div class="p-4 sm:p-5 flex-1">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate pr-2">
                                {{ $batch->name ?? "Batch #{$batch->id}" }}
                            </h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full flex-shrink-0
                                {{ $batch->status === 'open' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $batch->status === 'full' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $batch->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $batch->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($batch->status) }}
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
                            <div class="bg-gradient-to-r from-orange-500 to-purple-500 h-2 rounded-full transition-all duration-500 ease-out" 
                                 style="width: {{ $batch->members->count() / $batch->limit * 100 }}%"></div>
                        </div>

                        <!-- Batch Info -->
                        <div class="space-y-3">
                            <div class="flex items-center text-xs sm:text-sm text-gray-600">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-medium {{ $batch->type === 'trial' ? 'bg-green-50 text-green-700' : 'bg-orange-50 text-orange-700' }}">
                                    {{ $batch->type === 'trial' ? 'Trial' : 'Regular' }}
                                </span>
                                @if($batch->location)
                                    <span class="mx-2 text-gray-300">•</span>
                                    <span class="flex items-center truncate">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $batch->location }}
                                    </span>
                                @endif
                            </div>

                            @if($batch->interests->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($batch->interests->take(3) as $interest)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $interest->name }}
                                        </span>
                                    @endforeach
                                    @if($batch->interests->count() > 3)
                                        <span class="text-xs text-gray-500">+{{ $batch->interests->count() - 3 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-4 sm:px-5 py-3 bg-gray-50 border-t border-gray-100 mt-auto">
                        @if($batch->isFull())
                            <button 
                                wire:click="downloadContacts({{ $batch->id }})"
                                wire:loading.attr="disabled"
                                wire:target="downloadContacts({{ $batch->id }})"
                                class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 disabled:opacity-75 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                                <span wire:loading.remove wire:target="downloadContacts({{ $batch->id }})">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download Contacts
                                </span>
                                <span wire:loading wire:target="downloadContacts({{ $batch->id }})">
                                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                            </button>
                        @else
                            <div class="text-center text-sm font-medium text-gray-500">
                                @if($batch->status === 'open')
                                    Waiting for {{ $batch->limit - $batch->members->count() }} more members...
                                @elseif($batch->status === 'expired')
                                    Batch has expired
                                @else
                                    Batch closed
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $batches->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-orange-100 to-purple-100 rounded-2xl flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No batches found</h3>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                @if($filter === 'all')
                    You haven't joined any batches yet. Find a new batch to join!
                @elseif($filter === 'active')
                    You don't have any active batches waiting to be filled.
                @else
                    You don't have any closed or completed batches.
                @endif
            </p>
            @if($filter !== 'all')
                <div class="mt-6">
                    <button 
                        wire:click="$set('filter', 'all')"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                        View All Batches
                    </button>
                </div>
            @endif
        </div>
    @endif
    </div>
</div>