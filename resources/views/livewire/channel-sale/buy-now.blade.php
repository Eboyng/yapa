<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Header -->
            <div class="p-4 sm:p-6 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-purple-50">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Purchase Channel
                </h2>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Review the details and confirm your purchase</p>
            </div>

            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                    
                    <!-- Channel Details -->
                    <div class="order-2 lg:order-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Channel Details
                        </h3>
                        
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 sm:p-5 space-y-3 border border-gray-200">
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Channel Name:
                                </span>
                                <span class="font-semibold text-sm sm:text-base text-right">{{ $channelSale->channel_name }}</span>
                            </div>
                            
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    WhatsApp:
                                </span>
                                <span class="font-semibold text-sm sm:text-base text-right">{{ $channelSale->whatsapp_number }}</span>
                            </div>
                            
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Category:
                                </span>
                                <span class="font-semibold text-sm sm:text-base text-right">{{ $channelSale->categoryLabel }}</span>
                            </div>
                            
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Audience:
                                </span>
                                <span class="font-semibold text-sm sm:text-base text-right">{{ $channelSale->formattedAudienceSize }}</span>
                            </div>
                            
                            @if($channelSale->engagement_rate)
                                <div class="flex justify-between items-start">
                                    <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                        Engagement:
                                    </span>
                                    <span class="font-semibold text-sm sm:text-base text-right">{{ $channelSale->engagement_rate }}%</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Seller:
                                </span>
                                <span class="font-semibold text-sm sm:text-base text-right">{{ $channelSale->user->name }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center border-t border-gray-300 pt-3 mt-4">
                                <span class="text-gray-700 text-base sm:text-lg font-medium flex items-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Price:
                                </span>
                                <span class="font-bold text-xl sm:text-2xl text-green-600">{{ $channelSale->formattedPrice }}</span>
                            </div>
                        </div>
                        
                        @if($channelSale->description)
                            <div class="mt-4 sm:mt-6">
                                <h4 class="font-semibold text-gray-900 mb-2 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Description
                                </h4>
                                <p class="text-gray-700 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-3 sm:p-4 text-sm sm:text-base border border-gray-200">{{ $channelSale->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Purchase Form -->
                    <div class="order-1 lg:order-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                            Purchase Information
                        </h3>
                        
                        <form wire:submit.prevent="showConfirmationModal" class="space-y-4 sm:space-y-6">
                            
                            <!-- Wallet Balance Info -->
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 sm:p-5">
                                <h4 class="font-semibold text-blue-900 mb-3 text-sm sm:text-base flex items-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Payment Information
                                </h4>
                                <div class="space-y-2 text-xs sm:text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">Your Wallet Balance:</span>
                                        <span class="font-semibold text-blue-900">₦{{ number_format(auth()->user()->getNairaWallet()->balance, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">Purchase Amount:</span>
                                        <span class="font-semibold text-blue-900">{{ $channelSale->formattedPrice }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-blue-200 pt-2">
                                        <span class="text-blue-700">Remaining Balance:</span>
                                        <span class="font-semibold text-blue-900">
                                            ₦{{ number_format(auth()->user()->getNairaWallet()->balance - $channelSale->price, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Buyer Note -->
                            <div>
                                <label for="buyerNote" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                    Note to Seller (Optional)
                                </label>
                                <textarea id="buyerNote" wire:model="buyerNote" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm sm:text-base transition-colors duration-200"
                                          placeholder="Any questions or special requests for the seller..."></textarea>
                                @error('buyerNote') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Terms Agreement -->
                            <div>
                                <label class="flex items-start">
                                    <input type="checkbox" wire:model="agreedToTerms" 
                                           class="mt-1 rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-xs sm:text-sm text-gray-700">
                                        I agree to the <a href="#" class="text-orange-600 hover:underline">terms and conditions</a> 
                                        and understand that funds will be held in escrow until I confirm receipt of the channel.
                                    </span>
                                </label>
                                @error('agreedToTerms') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 pt-4">
                                <a href="{{ route('channel-sale.browse') }}"
                                   class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors text-center text-sm sm:text-base font-medium flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Back to Browse
                                </a>
                                <button type="submit" 
                                        class="flex-1 px-4 py-3 bg-gradient-to-r from-orange-500 to-purple-500 text-white rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base font-medium flex items-center justify-center"
                                        @if(auth()->user()->getNairaWallet()->balance < $channelSale->price) disabled @endif>
                                    @if(auth()->user()->getNairaWallet()->balance < $channelSale->price)
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        Insufficient Balance
                                    @else
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                        Purchase Channel
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmation)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4" wire:click="cancelPurchase">
            <div class="relative w-full max-w-md bg-white shadow-xl rounded-2xl border border-gray-200" wire:click.stop>
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-br from-yellow-100 to-orange-100 mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Purchase</h3>
                    <div class="mb-6">
                        <p class="text-sm text-gray-600">
                            Are you sure you want to purchase "<span class="font-semibold">{{ $channelSale->channel_name }}</span>" for <span class="font-semibold text-green-600">{{ $channelSale->formattedPrice }}</span>?
                        </p>
                        <p class="text-xs text-gray-500 mt-2">
                            This amount will be held in escrow until you confirm receipt.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                        <button wire:click="cancelPurchase"
                                class="flex-1 px-4 py-2 bg-gray-100 text-gray-800 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="confirmPurchase" wire:loading.attr="disabled"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-orange-500 to-purple-500 text-white text-sm font-medium rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 disabled:opacity-50 flex items-center justify-center">
                            <span wire:loading.remove class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Confirm
                            </span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>