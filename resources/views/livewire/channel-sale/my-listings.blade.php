<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">My Channel Listings</h2>
                <a href="{{ route('channel-sale.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Create New Listing
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <input type="text" wire:model.live="search" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Search channels...">
                </div>

                <!-- Status Filter -->
                <div>
                    <select wire:model.live="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Statuses</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <select wire:model.live="sortBy" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="created_at">Date Created</option>
                        <option value="channel_name">Channel Name</option>
                        <option value="price">Price</option>
                        <option value="audience_size">Audience Size</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <div>
                    <button wire:click="clearFilters"
                            class="w-full px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Listings -->
        <div class="p-6">
            @if($listings->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($listings as $listing)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <!-- Status Badge -->
                            <div class="flex justify-between items-start mb-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $listing->statusColor }}">
                                    {{ ucfirst($listing->status) }}
                                </span>
                                @if(!$listing->visibility)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Hidden
                                    </span>
                                @endif
                            </div>

                            <!-- Channel Info -->
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $listing->channel_name }}</h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $listing->categoryLabel }}</p>
                            <p class="text-sm text-gray-600 mb-2">{{ $listing->formattedAudienceSize }} members</p>
                            <p class="text-lg font-bold text-green-600 mb-4">{{ $listing->formattedPrice }}</p>

                            @if($listing->description)
                                <p class="text-sm text-gray-700 mb-4 line-clamp-3">{{ $listing->description }}</p>
                            @endif

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-2">
                                @if($listing->status === 'listed' || $listing->status === 'under_review')
                                    <button wire:click="editListing('{{ $listing->id }}')"
                                            class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200 transition-colors">
                                        Edit
                                    </button>
                                    
                                    @if($listing->visibility)
                                        <button wire:click="toggleVisibility('{{ $listing->id }}')"
                                                class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200 transition-colors">
                                            Hide
                                        </button>
                                    @else
                                        <button wire:click="toggleVisibility('{{ $listing->id }}')"
                                                class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded hover:bg-green-200 transition-colors">
                                            Show
                                        </button>
                                    @endif
                                    
                                    <button wire:click="removeListing('{{ $listing->id }}')"
                                            wire:confirm="Are you sure you want to remove this listing?"
                                            class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded hover:bg-red-200 transition-colors">
                                        Remove
                                    </button>
                                @endif

                                @if($listing->status === 'sold')
                                    <button wire:click="viewPurchases('{{ $listing->id }}')"
                                            class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded hover:bg-green-200 transition-colors">
                                        View Purchase
                                    </button>
                                @endif

                                @if($listing->status === 'removed')
                                    <button wire:click="relistChannel('{{ $listing->id }}')"
                                            class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200 transition-colors">
                                        Relist
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $listings->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">
                        <i class="fas fa-list-alt"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No listings found</h3>
                    <p class="text-gray-600 mb-4">You haven't created any channel listings yet.</p>
                    <a href="{{ route('channel-sale.create') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Create Your First Listing
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>