<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">

    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-end gap-3 items-center flex-wrap">
            <div>
                <a href="{{ route('channel-sale.create') }}"
                    class="flex gap-2 items-center bg-green-600 text-white rounded-xl text-sm shadow px-4 py-2 hover:bg-green-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg> Create Listing</a>
            </div>
            
            <div>
                <a href="{{ route('channel-sale.my-listings') }}"
                    class="flex gap-2 items-center bg-blue-600 text-white rounded-xl text-sm shadow px-4 py-2 hover:bg-blue-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg> My Listings</a>
            </div>
            
            <div>
                <a href="{{ route('channel-sale.my-purchases') }}"
                    class="flex gap-2 items-center bg-orange-600 text-white rounded-xl text-sm shadow px-4 py-2 hover:bg-orange-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg> My Purchases</a>
            </div>
            

            <div class="">
                <button onclick="openFilterModal()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4">
                        </path>
                    </svg>
                    <span class="text-sm font-medium">Filters</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

        <!-- Channel Listings -->
        @if ($channelSales->count() > 0)
            <div id="channel-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                @foreach ($channelSales as $channel)
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">

                        <div class="p-4 sm:p-6 flex-1">
                            <!-- Channel Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-2">
                                        {{ $channel->channel_name }}</h3>
                                    <p class="text-sm text-gray-600">by {{ $channel->user->name }}</p>
                                </div>

                                <span
                                    class="flex-shrink-0 ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $channel->categoryLabel }}
                                </span>
                            </div>

                            <!-- Description -->
                            @if ($channel->description)
                                <p class="text-gray-700 mb-4 line-clamp-3 text-sm">{{ $channel->description }}</p>
                            @endif

                            <!-- Channel Stats -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-blue-500 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    <span class="font-medium text-blue-600">{{ $channel->formattedAudienceSize }}</span>
                                </div>

                                @if ($channel->engagement_rate)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                        {{ $channel->engagement_rate }}%
                                    </div>
                                @endif
                            </div>

                            <!-- Price Display -->
                            <div class="mb-0">
                                <div class="flex items-center">
                                    <span
                                        class="text-2xl font-bold text-green-600">{{ $channel->formattedPrice }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Footer -->
                        <div class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100 mt-auto">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    Listed {{ $channel->created_at->diffForHumans() }}
                                </div>

                                <button wire:click="buyNow('{{ $channel->id }}')"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    Buy Now
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Load More Button / Loading Indicator -->
            @if ($hasMoreItems)
                <div class="mt-8 flex justify-center">
                    @if ($loading)
                        <div class="flex items-center justify-center py-4">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
                            <span class="ml-2 text-gray-600">Loading more channels...</span>
                        </div>
                    @else
                        <button wire:click="loadMore" id="load-more-btn"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-purple-500 text-white font-medium rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            Load More Channels
                        </button>
                    @endif
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div
                    class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-orange-100 to-purple-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No channels found</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    @if ($search || $category || $minPrice || $maxPrice || $minAudienceSize || $maxAudienceSize)
                        Try adjusting your filters to discover more channels.
                    @else
                        No channels are currently available for sale.
                    @endif
                </p>
                @if ($search || $category || $minPrice || $maxPrice || $minAudienceSize || $maxAudienceSize)
                    <button wire:click="clearFilters"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-purple-500 text-white font-medium rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Clear Filters
                    </button>
                @endif
            </div>
        @endif

    </div>

    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div
            class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeFilterModal()"
                aria-hidden="true"></div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

            <div
                class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4">
                            </path>
                        </svg>
                        Filter Channels
                    </h3>
                    <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search Channels
                        </label>
                        <input type="text" wire:model.live="search" placeholder="Search channels..."
                            id="search"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                </path>
                            </svg>
                            Category
                        </label>
                        <select wire:model.live="category" id="category"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                            <option value="">All Categories</option>
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Options -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                            </svg>
                            Sort By
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <button wire:click="sortBy('price')"
                                class="px-3 py-2 text-sm rounded-lg {{ $sortBy === 'price' ? 'bg-orange-100 text-orange-800 font-medium' : 'bg-gray-100 text-gray-700' }} hover:bg-orange-100 hover:text-orange-800 transition-colors">
                                Price
                                @if ($sortBy === 'price')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </button>
                            <button wire:click="sortBy('audience_size')"
                                class="px-3 py-2 text-sm rounded-lg {{ $sortBy === 'audience_size' ? 'bg-orange-100 text-orange-800 font-medium' : 'bg-gray-100 text-gray-700' }} hover:bg-orange-100 hover:text-orange-800 transition-colors">
                                Members
                                @if ($sortBy === 'audience_size')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </button>
                            <button wire:click="sortBy('created_at')"
                                class="px-3 py-2 text-sm rounded-lg {{ $sortBy === 'created_at' ? 'bg-orange-100 text-orange-800 font-medium' : 'bg-gray-100 text-gray-700' }} hover:bg-orange-100 hover:text-orange-800 transition-colors">
                                Date
                                @if ($sortBy === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </button>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                            Price Range
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" wire:model.live="minPrice"
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200"
                                placeholder="Min Price">
                            <input type="number" wire:model.live="maxPrice"
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200"
                                placeholder="Max Price">
                        </div>
                    </div>

                    <!-- Audience Size Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Audience Size
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" wire:model.live="minAudienceSize"
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200"
                                placeholder="Min Members">
                            <input type="number" wire:model.live="maxAudienceSize"
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200"
                                placeholder="Max Members">
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3 mt-8">
                    <button wire:click="clearFilters" onclick="closeFilterModal()"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Clear All
                    </button>
                    <button onclick="closeFilterModal()"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-purple-500 border border-transparent rounded-xl hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Apply Filters
                    </button>
                </div>
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

        // Auto-scroll infinite loading
        let isLoading = false;
        
        function checkScroll() {
            if (isLoading) return;
            
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            
            // Trigger load more when user is 200px from bottom
            if (scrollTop + windowHeight >= documentHeight - 200) {
                const loadMoreBtn = document.getElementById('load-more-btn');
                if (loadMoreBtn && !isLoading) {
                    isLoading = true;
                    loadMoreBtn.click();
                    
                    // Reset loading flag after a delay
                    setTimeout(() => {
                        isLoading = false;
                    }, 1000);
                }
            }
        }
        
        // Add scroll event listener
        window.addEventListener('scroll', checkScroll);
        
        // Listen for Livewire updates to reset loading state
        document.addEventListener('livewire:updated', () => {
            isLoading = false;
        });
    </script>
</div>
