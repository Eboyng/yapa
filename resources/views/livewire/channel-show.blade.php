<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Channel Ad Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6 sm:mb-8">
            <div class="p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex-1">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-2">{{ $channelAd->title }}</h1>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800">
                                {{ ucfirst($channelAd->status) }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span class="font-medium text-green-600">₦{{ number_format($channelAd->payment_per_channel) }}</span>
                                <span class="hidden sm:inline ml-1">per channel</span>
                            </span>
                            @if($channelAd->max_channels)
                                <span class="flex items-center font-semibold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">
                                    <span class="hidden sm:inline">Max </span>{{ $channelAd->max_channels }} channels
                                </span>
                            @endif
                        </div>
                        <p class="text-sm sm:text-base text-gray-700 leading-relaxed">{{ $channelAd->description }}</p>
                    </div>
                    @if($channelAd->media_url)
                        <div class="mt-4 sm:mt-0 sm:ml-6 flex justify-center sm:block">
                            <img src="{{ Storage::url($channelAd->media_url) }}" 
                                 alt="Ad Media" 
                                 class="w-24 h-24 sm:w-32 sm:h-32 object-cover rounded-xl border border-gray-200 shadow-sm">
                        </div>
                    @endif
                </div>
                
                <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                            @if($channelAd->end_date)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs sm:text-sm font-medium bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Deadline: </span>{{ $channelAd->end_date->format('M j, Y') }}
                                </span>
                            @endif
                        </div>
                        
                        @auth
                            <button wire:click="openBookingModal" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.648 9.168-4z"></path>
                                </svg>
                                <span class="hidden sm:inline">Book Advertisement</span>
                                <span class="sm:hidden">Book Ad</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                <span class="hidden sm:inline">Login to Book Ad</span>
                                <span class="sm:hidden">Login</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Ad Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Status</p>
                        <p class="text-base sm:text-lg font-semibold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent truncate">{{ ucfirst($channelAd->status) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.51-1.31c-.562-.649-1.413-1.076-2.353-1.253V5z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">
                            <span class="hidden sm:inline">Payment per Channel</span>
                            <span class="sm:hidden">Per Channel</span>
                        </p>
                        <p class="text-base sm:text-lg font-semibold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent truncate">₦{{ number_format($channelAd->payment_per_channel) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-200 sm:col-span-2 lg:col-span-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">
                            <span class="hidden sm:inline">Max Channels</span>
                            <span class="sm:hidden">Max</span>
                        </p>
                        <p class="text-base sm:text-lg font-semibold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent truncate">{{ number_format($channelAd->max_channels) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    @if($showBookingModal)
        <!-- Mobile Modal (Bottom Sheet) -->
        <div class="fixed inset-0 z-[60] overflow-hidden sm:hidden" aria-labelledby="mobile-modal-title" role="dialog" aria-modal="true">
            <style>
                /* Hide mobile navigation when modal is open */
                nav.fixed.bottom-0 {
                    display: none !important;
                }
            </style>
            <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closeBookingModal"></div>
            <div class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl transform transition-transform duration-300 ease-in-out max-h-[94vh] overflow-hidden">
                <form wire:submit.prevent="bookAd" class="flex flex-col h-full">
                    <!-- Mobile Header -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50 rounded-t-2xl">
                        <h3 class="text-lg font-semibold text-gray-900" id="mobile-modal-title">Book Ad</h3>
                        <button type="button" wire:click="closeBookingModal" class="p-2 text-gray-400 hover:text-gray-600 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Mobile Content -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-3">
                        <div class="text-xs text-gray-600 mb-3 p-3 bg-blue-50 rounded-xl">
                            {{ $channelAd->title }}
                        </div>
                        
                        <!-- Two Column Grid for Form Fields -->
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Title -->
                            <div>
                                <label for="mobile-title" class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                                <input type="text" 
                                       wire:model="title" 
                                       id="mobile-title" 
                                       class="w-full px-2 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-xs" 
                                       placeholder="Enter title">
                                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <!-- URL -->
                            <div>
                                <label for="mobile-url" class="block text-xs font-medium text-gray-700 mb-1">URL</label>
                                <input type="url" 
                                       wire:model="url" 
                                       id="mobile-url" 
                                       class="w-full px-2 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-xs" 
                                       placeholder="https://example.com">
                                @error('url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <!-- Images -->
                            <div>
                                <label for="mobile-images" class="block text-xs font-medium text-gray-700 mb-1">Images</label>
                                <input type="file" 
                                       wire:model="images" 
                                       id="mobile-images" 
                                       multiple 
                                       accept="image/*" 
                                       class="w-full text-xs text-gray-500 file:mr-1 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                @error('images.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <!-- Duration -->
                            <div>
                                <label for="mobile-duration" class="block text-xs font-medium text-gray-700 mb-1">Duration</label>
                                <select wire:model="duration_hours" 
                                        id="mobile-duration" 
                                        class="w-full px-2 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-xs">
                                    <option value="24">1 day</option>
                                    <option value="48">2 days</option>
                                    <option value="72">3 days</option>
                                    <option value="168">1 week</option>
                                    <option value="336">2 weeks</option>
                                    <option value="720">30 days</option>
                                </select>
                                @error('duration_hours') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        <!-- Description (Full Width) -->
                        <div>
                            <label for="mobile-description" class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                            <textarea wire:model="description" 
                                      id="mobile-description" 
                                      rows="2" 
                                      class="w-full px-2 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-xs" 
                                      placeholder="Describe your ad"></textarea>
                            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <!-- Channel Selection (Full Width) -->
                        <div>
                            <label for="mobile-channel" class="block text-xs font-medium text-gray-700 mb-1">Select Channel *</label>
                            @if(count($available_channels) > 0)
                                <select wire:model="selected_channel_id" 
                                        id="mobile-channel" 
                                        class="w-full px-2 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-xs">
                                    <option value="">Choose a channel...</option>
                                    @foreach($available_channels as $channel)
                                        <option value="{{ $channel['id'] }}">{{ $channel['name'] }} ({{ number_format($channel['follower_count']) }} followers - {{ ucfirst($channel['niche']) }})</option>
                                    @endforeach
                                </select>
                            @else
                                <div class="p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-xs text-yellow-800">No channels available that match this ad's criteria.</p>
                                </div>
                            @endif
                            @error('selected_channel_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <!-- Payment Method -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Payment Method</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <input type="radio" 
                                           wire:model="payment_method" 
                                           value="wallet" 
                                           class="focus:ring-orange-500 h-3 w-3 text-orange-600 border-gray-300">
                                    <div class="ml-2">
                                        <div class="text-xs text-gray-700 font-medium">Wallet</div>
                                        <div class="text-xs text-gray-500">₦{{ number_format($userNairaBalance) }}</div>
                                        @if($userNairaBalance < $total_amount)
                                            <div class="text-xs text-red-500">Insufficient</div>
                                        @endif
                                    </div>
                                </label>
                                <label class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <input type="radio" 
                                           wire:model="payment_method" 
                                           value="paystack" 
                                           class="focus:ring-orange-500 h-3 w-3 text-orange-600 border-gray-300">
                                    <div class="ml-2">
                                        <div class="text-xs text-gray-700 font-medium">Paystack</div>
                                        <div class="text-xs text-gray-500">Card/Bank</div>
                                    </div>
                                </label>
                            </div>
                            @error('payment_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <!-- Total Amount -->
                        <div class="bg-gradient-to-r from-orange-50 to-purple-50 p-3 rounded-lg border border-orange-200">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-medium text-gray-700">Total:</span>
                                <span class="text-sm font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">₦{{ number_format($total_amount) }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                ₦{{ number_format($channelAd->payment_per_channel) }}/channel × {{ $duration_hours/24 }} day(s)
                            </p>
                        </div>
                    </div>
                    
                    <!-- Mobile Footer -->
                    <div class="p-4 bg-gray-50 border-t border-gray-200 space-y-2">
                        <button type="submit" 
                                wire:loading.attr="disabled" 
                                class="w-full inline-flex justify-center items-center px-4 py-3 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 transition-all duration-200">
                            <span wire:loading.remove>Book Advertisement</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                        <button type="button" 
                                wire:click="closeBookingModal" 
                                class="w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Desktop Modal -->
        <div class="hidden sm:block fixed inset-0 z-50 overflow-y-auto" aria-labelledby="desktop-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeBookingModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="bookAd">
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="w-full">
                                <h3 class="text-xl leading-6 font-semibold text-gray-900 mb-6" id="desktop-modal-title">
                                    Book Advertisement: {{ $channelAd->title }}
                                </h3>
                                
                                <div class="space-y-6">
                                    <!-- Title -->
                                    <div>
                                        <label for="desktop-title" class="block text-sm font-medium text-gray-700 mb-2">Title (Optional)</label>
                                        <input type="text" 
                                               wire:model="title" 
                                               id="desktop-title" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500" 
                                               placeholder="Enter ad title">
                                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    
                                    <!-- Description -->
                                    <div>
                                        <label for="desktop-description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                                        <textarea wire:model="description" 
                                                  id="desktop-description" 
                                                  rows="3" 
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500" 
                                                  placeholder="Describe your advertisement"></textarea>
                                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    
                                    <!-- Channel Selection -->
                                    <div>
                                        <label for="desktop-channel" class="block text-sm font-medium text-gray-700 mb-2">Select Channel *</label>
                                        @if(count($available_channels) > 0)
                                            <select wire:model="selected_channel_id" 
                                                    id="desktop-channel" 
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                                <option value="">Choose a channel...</option>
                                                @foreach($available_channels as $channel)
                                                    <option value="{{ $channel['id'] }}">{{ $channel['name'] }} ({{ number_format($channel['follower_count']) }} followers - {{ ucfirst($channel['niche']) }})</option>
                                                @endforeach
                                            </select>
                                            <p class="mt-1 text-sm text-gray-500">Select the channel where you want your ad to be displayed.</p>
                                        @else
                                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                                                <div class="flex">
                                                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <div>
                                                        <h4 class="text-sm font-medium text-yellow-800">No Available Channels</h4>
                                                        <p class="text-sm text-yellow-700 mt-1">There are currently no channels available that match this ad's criteria (niche, minimum followers, etc.).</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @error('selected_channel_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    
                                    <!-- URL -->
                                    <div>
                                        <label for="desktop-url" class="block text-sm font-medium text-gray-700 mb-2">URL (Optional)</label>
                                        <input type="url" 
                                               wire:model="url" 
                                               id="desktop-url" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500" 
                                               placeholder="https://example.com">
                                        @error('url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    
                                    <!-- Images -->
                                    <div>
                                        <label for="desktop-images" class="block text-sm font-medium text-gray-700 mb-2">Images (Optional)</label>
                                        <input type="file" 
                                               wire:model="images" 
                                               id="desktop-images" 
                                               multiple 
                                               accept="image/*" 
                                               class="w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                        @error('images.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    
                                    <!-- Duration -->
                                    <div>
                                        <label for="desktop-duration" class="block text-sm font-medium text-gray-700 mb-2">Duration (Hours)</label>
                                        <select wire:model="duration_hours" 
                                                id="desktop-duration" 
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                            <option value="24">24 hours (1 day)</option>
                                            <option value="48">48 hours (2 days)</option>
                                            <option value="72">72 hours (3 days)</option>
                                            <option value="168">168 hours (1 week)</option>
                                            <option value="336">336 hours (2 weeks)</option>
                                            <option value="720">720 hours (30 days)</option>
                                        </select>
                                        @error('duration_hours') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    
                                    <!-- Payment Method -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
                                        <div class="space-y-3">
                                            <label class="flex items-center">
                                                <input type="radio" 
                                                       wire:model="payment_method" 
                                                       value="wallet" 
                                                       class="focus:ring-orange-500 h-4 w-4 text-orange-600 border-gray-300">
                                                <span class="ml-3 text-sm text-gray-700">
                                                    Wallet (Balance: ₦{{ number_format($userNairaBalance) }})
                                                    @if($userNairaBalance < $total_amount)
                                                        <span class="text-red-500 text-xs block">(Insufficient balance - will use Paystack)</span>
                                                    @endif
                                                </span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" 
                                                       wire:model="payment_method" 
                                                       value="paystack" 
                                                       class="focus:ring-orange-500 h-4 w-4 text-orange-600 border-gray-300">
                                                <span class="ml-3 text-sm text-gray-700">Paystack (Card/Bank Transfer)</span>
                                            </label>
                                        </div>
                                        @error('payment_method') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    
                                    <!-- Total Amount -->
                                    <div class="bg-gradient-to-r from-orange-50 to-purple-50 p-6 rounded-xl border border-orange-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-medium text-gray-700">Total Amount:</span>
                                            <span class="text-2xl font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">₦{{ number_format($total_amount) }}</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-2">
                                            Rate: ₦{{ number_format($channelAd->payment_per_channel) }}/channel × {{ $duration_hours/24 }} day(s)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse space-x-3 space-x-reverse rounded-b-2xl">
                            <button type="submit" 
                                    wire:loading.attr="disabled" 
                                    class="inline-flex justify-center items-center px-6 py-3 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 transition-all duration-200 transform hover:scale-105">
                                <span wire:loading.remove>Book Advertisement</span>
                                <span wire:loading class="flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                            <button type="button" 
                                    wire:click="closeBookingModal" 
                                    class="inline-flex justify-center items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
    
    /* Custom scrollbar for mobile modal */
    .overflow-y-auto::-webkit-scrollbar {
        width: 4px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #f97316, #a855f7);
        border-radius: 4px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #ea580c, #9333ea);
    }
</style>

</div>

