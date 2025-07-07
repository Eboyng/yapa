<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Channel Advertising Opportunities</h1>
        <p class="text-gray-600">Find and apply for advertising opportunities on WhatsApp channels</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Ads</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Budget</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['total_budget']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg. Payment</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['avg_payment']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" wire:model.live="search" placeholder="Search ads..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Niche Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Target Niche</label>
                <select wire:model.live="nicheFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Niches</option>
                    @foreach($niches as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Budget Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Min. Payment</label>
                <select wire:model.live="budgetFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Any Amount</option>
                    <option value="1000">₦1,000+</option>
                    <option value="5000">₦5,000+</option>
                    <option value="10000">₦10,000+</option>
                    <option value="25000">₦25,000+</option>
                    <option value="50000">₦50,000+</option>
                </select>
            </div>
        </div>
        
        <div class="mt-4 flex justify-between items-center">
            <button wire:click="clearFilters" class="text-sm text-gray-600 hover:text-gray-800">
                Clear Filters
            </button>
            
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Sort by:</span>
                <button wire:click="sortBy('payment_per_channel')" class="text-sm {{ $sortBy === 'payment_per_channel' ? 'text-blue-600 font-medium' : 'text-gray-600' }} hover:text-blue-600">
                    Payment {{ $sortBy === 'payment_per_channel' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                </button>
                <button wire:click="sortBy('created_at')" class="text-sm {{ $sortBy === 'created_at' ? 'text-blue-600 font-medium' : 'text-gray-600' }} hover:text-blue-600">
                    Date {{ $sortBy === 'created_at' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                </button>
                <button wire:click="sortBy('end_date')" class="text-sm {{ $sortBy === 'end_date' ? 'text-blue-600 font-medium' : 'text-gray-600' }} hover:text-blue-600">
                    Deadline {{ $sortBy === 'end_date' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Channel Ads Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        @forelse($channelAds as $ad)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <!-- Ad Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $ad->title }}</h3>
                            <p class="text-sm text-gray-600">by {{ $ad->creator->name ?? 'Admin' }}</p>
                        </div>
                        
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'active' => 'bg-green-100 text-green-800',
                                'paused' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$ad->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($ad->status) }}
                        </span>
                    </div>
                    
                    <!-- Description -->
                    <p class="text-gray-700 mb-4 line-clamp-3">{{ $ad->description }}</p>
                    
                    <!-- Ad Details -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span class="font-medium text-green-600">₦{{ number_format($ad->payment_per_channel) }}</span>
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $ad->duration }} days
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Min. {{ number_format($ad->min_followers) }} followers
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            {{ $ad->applications_count ?? 0 }}/{{ $ad->max_channels }} applied
                        </div>
                    </div>
                    
                    <!-- Target Niches -->
                    @if($ad->target_niches)
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Target Niches:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach(json_decode($ad->target_niches, true) ?? [] as $niche)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $niches[$niche] ?? ucfirst($niche) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Deadline -->
                    @if($ad->end_date)
                        <div class="mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Deadline: {{ $ad->end_date->format('M j, Y') }}
                                @if($ad->end_date->isPast())
                                    <span class="ml-2 text-red-600 font-medium">(Expired)</span>
                                @elseif($ad->end_date->diffInDays() <= 3)
                                    <span class="ml-2 text-orange-600 font-medium">({{ $ad->end_date->diffForHumans() }})</span>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <!-- Progress Bar -->
                    @php
                        $progress = $ad->max_channels > 0 ? (($ad->applications_count ?? 0) / $ad->max_channels) * 100 : 0;
                    @endphp
                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Applications Progress</span>
                            <span>{{ number_format($progress, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ min($progress, 100) }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Apply Button -->
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Posted {{ $ad->created_at->diffForHumans() }}
                        </div>
                        
                        @auth
                            @if($ad->status === 'active' && !$ad->end_date?->isPast() && $progress < 100)
                                @php
                                    $userChannel = auth()->user()->channels()->where('status', 'approved')->first();
                                    $hasApplied = $userChannel && $ad->applications()->where('channel_id', $userChannel->id)->exists();
                                    $meetsRequirements = $userChannel && 
                                        $userChannel->follower_count >= $ad->min_followers &&
                                        (empty($ad->target_niches) || in_array($userChannel->niche, json_decode($ad->target_niches, true) ?? []));
                                @endphp
                                
                                @if($hasApplied)
                                    <span class="inline-flex items-center px-3 py-2 border border-green-300 rounded-md text-sm font-medium text-green-700 bg-green-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Applied
                                    </span>
                                @elseif(!$userChannel)
                                    <span class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-500 bg-gray-50">
                                        No approved channel
                                    </span>
                                @elseif(!$meetsRequirements)
                                    <span class="inline-flex items-center px-3 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-red-50">
                                        Requirements not met
                                    </span>
                                @else
                                    <button wire:click="applyToAd({{ $ad->id }})" 
                                            wire:loading.attr="disabled"
                                            wire:target="applyToAd({{ $ad->id }})"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                                        <span wire:loading.remove wire:target="applyToAd({{ $ad->id }})">Apply Now</span>
                                        <span wire:loading wire:target="applyToAd({{ $ad->id }})" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Applying...
                                        </span>
                                    </button>
                                @endif
                            @else
                                <span class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-500 bg-gray-50">
                                    @if($ad->status !== 'active')
                                        Not active
                                    @elseif($ad->end_date?->isPast())
                                        Expired
                                    @else
                                        Full
                                    @endif
                                </span>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Login to Apply
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No ads found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria.</p>
            </div>
        @endforelse
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Success!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Error!</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    <div class="mt-6">
        {{ $channelAds->links() }}
    </div>
</div>