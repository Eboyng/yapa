<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">WhatsApp Channel Marketplace</h2>
            <p class="text-gray-600 mt-2">Discover and purchase established WhatsApp channels</p>
        </div>

        <!-- Filters -->
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Search -->
                <div>
                    <input type="text" wire:model.live="search" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Search channels...">
                </div>

                <!-- Category Filter -->
                <div>
                    <select wire:model.live="category" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range -->
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" wire:model.live="minPrice" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Min Price">
                    <input type="number" wire:model.live="maxPrice" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Max Price">
                </div>

                <!-- Audience Size Range -->
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" wire:model.live="minAudienceSize" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Min Members">
                    <input type="number" wire:model.live="maxAudienceSize" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Max Members">
                </div>
            </div>

            <!-- Sort and Clear -->
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Sort by:</span>
                    <button wire:click="sortBy('price')" 
                            class="text-sm {{ $sortBy === 'price' ? 'text-blue-600 font-semibold' : 'text-gray-600' }} hover:text-blue-600">
                        Price
                        @if($sortBy === 'price')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </button>
                    <button wire:click="sortBy('audience_size')" 
                            class="text-sm {{ $sortBy === 'audience_size' ? 'text-blue-600 font-semibold' : 'text-gray-600' }} hover:text-blue-600">
                        Members
                        @if($sortBy === 'audience_size')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </button>
                    <button wire:click="sortBy('created_at')" 
                            class="text-sm {{ $sortBy === 'created_at' ? 'text-blue-600 font-semibold' : 'text-gray-600' }} hover:text-blue-600">
                        Date
                        @if($sortBy === 'created_at')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </button>
                </div>
                <button wire:click="clearFilters"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Channel Listings -->
        <div class="p-6">
            @if($channelSales->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($channelSales as $channel)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
                            <!-- Channel Header -->
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $channel->channel_name }}</h3>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $channel->categoryLabel }}
                                </span>
                            </div>

                            <!-- Channel Stats -->
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Members:</span>
                                    <span class="font-semibold">{{ $channel->formattedAudienceSize }}</span>
                                </div>
                                @if($channel->engagement_rate)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Engagement:</span>
                                        <span class="font-semibold">{{ $channel->engagement_rate }}%</span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Seller:</span>
                                    <span class="font-semibold">{{ $channel->user->name }}</span>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($channel->description)
                                <p class="text-sm text-gray-700 mb-4 line-clamp-3">{{ $channel->description }}</p>
                            @endif

                            <!-- Price and Action -->
                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-2xl font-bold text-green-600">{{ $channel->formattedPrice }}</span>
                                    <button wire:click="buyNow('{{ $channel->id }}')"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        Buy Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $channelSales->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No channels found</h3>
                    <p class="text-gray-600 mb-4">
                        @if($search || $category || $minPrice || $maxPrice || $minAudienceSize || $maxAudienceSize)
                            Try adjusting your filters to see more results.
                        @else
                            No channels are currently available for sale.
                        @endif
                    </p>
                    @if($search || $category || $minPrice || $maxPrice || $minAudienceSize || $maxAudienceSize)
                        <button wire:click="clearFilters"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Clear Filters
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>