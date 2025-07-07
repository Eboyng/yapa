<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Available Batches</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Join batches to connect with like-minded people and grow your network
                    </p>
                </div>
                
                <!-- Credits Balance -->
                <div class="mt-4 sm:mt-0">
                    <div class="bg-indigo-50 dark:bg-indigo-900 rounded-lg px-4 py-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-indigo-900 dark:text-indigo-100">Your Credits</p>
                                <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($creditsBalance) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Filter Batches</h3>
                <button wire:click="toggleFilters" 
                        class="mt-2 sm:mt-0 inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                </button>
            </div>

            @if($showFilters)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Location Filter -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Location
                        </label>
                        <select wire:model.live="filters.location" 
                                id="location"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Batch Type
                        </label>
                        <select wire:model.live="filters.type" 
                                id="type"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all">All Types</option>
                            <option value="trial">Trial Batches</option>
                            <option value="regular">Regular Batches</option>
                        </select>
                    </div>

                    <!-- Interests Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Interests
                        </label>
                        <div class="max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-700">
                            @foreach($interests as $interest)
                                <label class="flex items-center py-1">
                                    <input type="checkbox" 
                                           wire:model.live="filters.interests" 
                                           value="{{ $interest->id }}"
                                           class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $interest->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button wire:click="clearFilters" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear Filters
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-md p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="ml-3 text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-md p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Batches Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        @if($batches->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($batches as $batch)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <!-- Batch Header -->
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $batch->name }}</h3>
                                    
                                    @if($batch->type === 'trial')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mb-3">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Trial Batch
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Members</div>
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $batch->getCurrentMemberCount() }}/{{ $batch->limit }}
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-4">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $batch->getFillPercentage() }}%"></div>
                            </div>

                            <!-- Batch Details -->
                            <div class="space-y-3">
                                @if($batch->location)
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $batch->location }}
                                    </div>
                                @endif

                                @if($batch->interests->count() > 0)
                                    <div class="flex items-start text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($batch->interests->take(3) as $interest)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                    {{ $interest->name }}
                                                </span>
                                            @endforeach
                                            @if($batch->interests->count() > 3)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">+{{ $batch->interests->count() - 3 }} more</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if($batch->type === 'regular')
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                        </svg>
                                        {{ number_format($batch->cost_in_credits) }} credits
                                    </div>
                                @else
                                    <div class="flex items-center text-sm text-green-600 dark:text-green-400">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Free Trial
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Batch Actions -->
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                            @php
                                $user = Auth::user();
                                $isMember = $batch->members()->where('user_id', $user->id)->exists();
                                $canJoin = $batch->canUserJoin($user);
                                $isFull = $batch->isFull();
                            @endphp

                            <div class="flex space-x-3">
                                @if($isMember)
                                    @if($isFull)
                                        <!-- Download Button -->
                                        <button wire:click="downloadVcf({{ $batch->id }})" 
                                                class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download VCF
                                        </button>
                                    @else
                                        <!-- Waiting Status -->
                                        <div class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600">
                                            <svg class="w-4 h-4 mr-2 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Waiting for batch to fill
                                        </div>
                                    @endif
                                @else
                                    @if($canJoin)
                                        <!-- Join Button -->
                                        <button wire:click="joinBatch({{ $batch->id }})" 
                                                @if($isProcessing) disabled @endif
                                                class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                            @if($isProcessing)
                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Joining...
                                            @else
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Join Batch
                                            @endif
                                        </button>
                                    @else
                                        <!-- Disabled Join Button with Tooltip -->
                                        <div class="flex-1 relative group">
                                            <button disabled 
                                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-600 cursor-not-allowed">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                </svg>
                                                @if($isFull)
                                                    Batch Full
                                                @elseif($batch->type === 'trial' && $user->hasTrialBatchMembership())
                                                    Trial Used
                                                @elseif($batch->type === 'regular' && !$user->hasSufficientCredits($batch->cost_in_credits))
                                                    Insufficient Credits
                                                @else
                                                    Cannot Join
                                                @endif
                                            </button>
                                            
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-sm text-white bg-gray-900 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                @if($isFull)
                                                    This batch is already full
                                                @elseif($batch->type === 'trial' && $user->hasTrialBatchMembership())
                                                    You can only join one trial batch
                                                @elseif($batch->type === 'regular' && !$user->hasSufficientCredits($batch->cost_in_credits))
                                                    You need {{ number_format($batch->cost_in_credits) }} credits
                                                @else
                                                    Unable to join this batch
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
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
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No batches found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if(array_filter($filters))
                        Try adjusting your filters to see more batches.
                    @else
                        There are no available batches at the moment. Check back later!
                    @endif
                </p>
                @if(array_filter($filters))
                    <div class="mt-6">
                        <button wire:click="clearFilters" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clear Filters
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Quick Actions Floating Button -->
    <div class="fixed bottom-6 right-6">
        <div class="relative group">
            <a href="{{ route('credits.purchase') }}" 
               class="inline-flex items-center px-4 py-3 border border-transparent text-sm font-medium rounded-full text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg hover:shadow-xl transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                </svg>
                Buy Credits
            </a>
        </div>
    </div>
</div>