<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">My Channel Purchases</h2>
            <p class="text-gray-600 mt-2">Track your channel purchase history and manage pending transactions</p>
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
                        <option value="created_at">Purchase Date</option>
                        <option value="price">Price</option>
                        <option value="status">Status</option>
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

        <!-- Purchases -->
        <div class="p-6">
            @if($purchases->count() > 0)
                <div class="space-y-4">
                    @foreach($purchases as $purchase)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                                <!-- Channel Info -->
                                <div class="lg:col-span-2">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        {{ $purchase->channelSale->channel_name }}
                                    </h3>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <p><span class="font-medium">Category:</span> {{ $purchase->channelSale->categoryLabel }}</p>
                                        <p><span class="font-medium">Members:</span> {{ $purchase->channelSale->formattedAudienceSize }}</p>
                                        <p><span class="font-medium">Seller:</span> {{ $purchase->channelSale->user->name }}</p>
                                        <p><span class="font-medium">WhatsApp:</span> {{ $purchase->channelSale->whatsapp_number }}</p>
                                    </div>
                                </div>

                                <!-- Purchase Details -->
                                <div>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Price:</span>
                                            <span class="font-semibold text-green-600">{{ $purchase->formattedPrice }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Date:</span>
                                            <span class="text-sm">{{ $purchase->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Status:</span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $purchase->statusColor }}">
                                                {{ ucfirst($purchase->status) }}
                                            </span>
                                        </div>
                                        @if($purchase->completed_at)
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600">Completed:</span>
                                                <span class="text-sm">{{ $purchase->completed_at->format('M d, Y') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col space-y-2">
                                    @if($purchase->status === 'in_escrow')
                                        <button wire:click="confirmPurchase('{{ $purchase->id }}')"
                                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm">
                                            <i class="fas fa-check mr-1"></i>
                                            Confirm Receipt
                                        </button>
                                    @endif

                                    @if($purchase->status === 'pending')
                                        <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-md text-sm text-center">
                                            <i class="fas fa-clock mr-1"></i>
                                            Processing Payment
                                        </span>
                                    @endif

                                    @if($purchase->status === 'completed')
                                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-md text-sm text-center">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Purchase Complete
                                        </span>
                                    @endif

                                    @if($purchase->status === 'refunded')
                                        <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md text-sm text-center">
                                            <i class="fas fa-undo mr-1"></i>
                                            Refunded
                                        </span>
                                    @endif

                                    @if($purchase->status === 'failed')
                                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded-md text-sm text-center">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Failed
                                        </span>
                                    @endif

                                    <button wire:click="viewPurchase('{{ $purchase->id }}')"
                                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors text-sm">
                                        <i class="fas fa-eye mr-1"></i>
                                        View Details
                                    </button>
                                </div>
                            </div>

                            <!-- Admin Note -->
                            @if($purchase->admin_note)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-900 mb-1">Notes:</h4>
                                    <p class="text-sm text-gray-600 bg-gray-50 rounded p-2">{{ $purchase->admin_note }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $purchases->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No purchases found</h3>
                    <p class="text-gray-600 mb-4">
                        @if($search || $status)
                            No purchases match your current filters.
                        @else
                            You haven't purchased any channels yet.
                        @endif
                    </p>
                    @if($search || $status)
                        <button wire:click="clearFilters"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Clear Filters
                        </button>
                    @else
                        <a href="{{ route('channel-sale.browse') }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Browse Channels
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>