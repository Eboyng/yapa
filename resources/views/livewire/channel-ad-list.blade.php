<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">

    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center">
            <div>
                <!-- You can add a title here if you want, e.g., <h1 class="text-2xl font-bold text-gray-800">Ad Marketplace</h1> -->
            </div>

            <button onclick="openFilterModal()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
                <span class="text-sm font-medium">Filters</span>
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        
        <!-- Display Success/Error Messages Above the Grid -->
        @if (session()->has('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 animate-fade-in mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 animate-fade-in mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif
        
        <!-- Check if there are any ads to display -->
        @if($channelAds->isNotEmpty())
            <!-- Channel Ads Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($channelAds as $ad)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">
                        
                        <div class="p-4 sm:p-6 flex-1">
                            <!-- Ad Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-2">{{ $ad->title }}</h3>
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
                                <span class="flex-shrink-0 ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$ad->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($ad->status) }}
                                </span>
                            </div>
                            
                            <!-- Description -->
                            <p class="text-gray-700 mb-4 line-clamp-3 text-sm">{{ $ad->description }}</p>
                            
                            <!-- Ad Details -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    <span class="font-medium text-green-600">₦{{ number_format($ad->payment_per_channel) }}</span>
                                </div>
                                
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $ad->duration }} days
                                </div>
                                
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ number_format($ad->min_followers) }}+
                                </div>
                                
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    {{ $ad->applications_count ?? 0 }}/{{ $ad->max_channels }}
                                </div>
                            </div>
                            
                            <!-- Target Niches -->
                            @if($ad->target_niches)
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Target Niches:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(json_decode($ad->target_niches, true) ?? [] as $niche)
                                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
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
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <span>Progress</span>
                                    <span>{{ number_format($progress, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-orange-500 to-purple-500 h-2 rounded-full transition-all duration-500 ease-out" style="width: {{ min($progress, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Footer -->
                        <div class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100 mt-auto">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    {{ $ad->created_at->diffForHumans() }}
                                </div>
                                
                                @auth
                                    <button wire:click="bookAd({{ $ad->id }})" 
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                        Book Ad
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                                        Login to Apply
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $channelAds->links() }}
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-orange-100 to-purple-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No ads found</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    We couldn't find any ads matching your criteria. Try adjusting your filters to discover more opportunities.
                </p>
            </div>
        @endif
        
    </div>

    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeFilterModal()" aria-hidden="true"></div>
            
            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Filter Ads
                    </h3>
                    <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search
                        </label>
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="Search by title or description..."
                               id="search"
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                    </div>
                    
                    <!-- Niche Filter -->
                    <div>
                        <label for="niche" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Target Niche
                        </label>
                        <select wire:model.live="nicheFilter" 
                                id="niche"
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                            <option value="">All Niches</option>
                            @foreach($niches as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location Filter -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Location
                        </label>
                        <select wire:model.live="locationFilter" 
                                id="location"
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Budget Filter -->
                    <div>
                        <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Min. Payment
                        </label>
                        <select wire:model.live="budgetFilter" 
                                id="budget"
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                            <option value="">Any Amount</option>
                            <option value="1000">₦1,000+</option>
                            <option value="5000">₦5,000+</option>
                            <option value="10000">₦10,000+</option>
                            <option value="25000">₦25,000+</option>
                            <option value="50000">₦50,000+</option>
                        </select>
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


    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fade-in 0.3s ease-out forwards;
        }

        @keyframes fade-out {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
        
        .animate-fade-out {
            animation: fade-out 0.3s ease-in forwards;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

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
        
        // Auto-hide flash messages
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const flashMessages = document.querySelectorAll('.animate-fade-in');
                flashMessages.forEach(message => {
                    // Check if it's a notification message before fading out
                    if (message.querySelector('svg')) { 
                        message.classList.remove('animate-fade-in');
                        message.classList.add('animate-fade-out');
                        setTimeout(() => {
                            if (message.parentNode) {
                                message.parentNode.removeChild(message);
                            }
                        }, 300);
                    }
                });
            }, 5000);
        });
    </script>
</div>