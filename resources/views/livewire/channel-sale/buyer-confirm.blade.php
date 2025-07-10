<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Header -->
            <div class="p-4 sm:p-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    Confirm Channel Receipt
                </h2>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Please confirm that you have received access to the channel</p>
            </div>

            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                    
                    <!-- Purchase Details -->
                    <div class="order-2 lg:order-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Purchase Details
                        </h3>
                        
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 sm:p-5 space-y-3 border border-gray-200">
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Channel Name:
                                </span>
                                <span class="font-semibold text-sm sm:text-base text-right">{{ $purchase->channelSale->channel_name }}</span>
                            </div>
                            
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    WhatsApp:
                                </span>
                                <span class="font-semibold text-sm sm:text-base text-right">{{ $purchase->channelSale->whatsapp_number }}</span>
                            </div>
                            
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Category:
                                </span>
                                <span class="font-semibold text-sm sm:text-base text-right">{{ $purchase->channelSale->categoryLabel }}</span>
                            </div>
                            
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Audience:
                                </span>
                                <span class="font-semibold text-sm sm:text-base text-right">{{ $purchase->channelSale->formattedAudienceSize }}</span>
                            </div>
                            
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Seller:
                                </span>
                                <span class="font-semibold text-sm sm:text-base text-right">{{ $purchase->channelSale->user->name }}</span>
                            </div>
                            
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Purchase Date:
                                </span>
                                <span class="font-semibold text-sm sm:text-base text-right">{{ $purchase->created_at->format('M d, Y') }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center border-t border-gray-300 pt-3 mt-4">
                                <span class="text-gray-700 text-base sm:text-lg font-medium flex items-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Amount Paid:
                                </span>
                                <span class="font-bold text-xl sm:text-2xl text-green-600">{{ $purchase->formattedPrice }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Status:
                                </span>
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $purchase->statusColor }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </div>
                        </div>
                        
                        @if($purchase->channelSale->description)
                            <div class="mt-4 sm:mt-6">
                                <h4 class="font-semibold text-gray-900 mb-2 text-sm sm:text-base flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                    </svg>
                                    Channel Description
                                </h4>
                                <p class="text-gray-700 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-3 sm:p-4 text-sm sm:text-base border border-gray-200">{{ $purchase->channelSale->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Confirmation Form -->
                    <div class="order-1 lg:order-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                            Confirmation
                        </h3>
                        
                        <!-- Instructions -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 sm:p-5 mb-4 sm:mb-6">
                            <h4 class="font-semibold text-blue-900 mb-2 text-sm sm:text-base flex items-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Before Confirming:
                            </h4>
                            <ul class="text-xs sm:text-sm text-blue-800 space-y-1">
                                <li class="flex items-start">
                                    <svg class="w-3 h-3 mt-0.5 mr-2 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Verify you have admin access to the WhatsApp channel
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-3 h-3 mt-0.5 mr-2 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Check that the channel details match what was advertised
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-3 h-3 mt-0.5 mr-2 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Ensure the audience size is as described
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-3 h-3 mt-0.5 mr-2 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Test that you can post and manage the channel
                                </li>
                            </ul>
                        </div>

                        <form wire:submit.prevent="showConfirmationModal" class="space-y-4 sm:space-y-6">
                            
                            <!-- Confirmation Note -->
                            <div>
                                <label for="confirmationNote" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                    Confirmation Note (Optional)
                                </label>
                                <textarea id="confirmationNote" wire:model="confirmationNote" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm sm:text-base transition-colors duration-200"
                                          placeholder="Any feedback about the channel or transaction..."></textarea>
                                @error('confirmationNote') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Warning -->
                            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-4 sm:p-5">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-yellow-800 flex items-center">
                                            Important Notice
                                        </h4>
                                        <p class="text-xs sm:text-sm text-yellow-700 mt-1">
                                            Once you confirm receipt, the funds will be released to the seller and this action cannot be undone.
                                            Only confirm if you have successfully received access to the channel.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <button type="submit" 
                                        class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-200 font-medium text-sm sm:text-base flex items-center justify-center shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Confirm Receipt & Release Payment
                                </button>
                                
                                <button type="button" wire:click="requestRefund"
                                        wire:confirm="Are you sure you want to request a refund? This will return the funds to your wallet."
                                        class="w-full px-4 py-3 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-xl hover:from-red-600 hover:to-pink-600 transition-all duration-200 font-medium text-sm sm:text-base flex items-center justify-center shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                    </svg>
                                    Request Refund
                                </button>
                                
                                <a href="{{ route('channel-sale.my-purchases') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors text-center text-sm sm:text-base font-medium flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Back to My Purchases
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmation)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4" wire:click="cancelConfirmation">
            <div class="relative w-full max-w-md bg-white shadow-xl rounded-2xl border border-gray-200" wire:click.stop>
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-br from-green-100 to-emerald-100 mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Receipt</h3>
                    <div class="mb-6">
                        <p class="text-sm text-gray-600">
                            Are you sure you want to confirm receipt of "<span class="font-semibold">{{ $purchase->channelSale->channel_name }}</span>"?
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            This will release <span class="font-semibold text-green-600">{{ $purchase->formattedPrice }}</span> to the seller.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                        <button wire:click="cancelConfirmation"
                                class="flex-1 px-4 py-2 bg-gray-100 text-gray-800 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="confirmReceived" wire:loading.attr="disabled"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-sm font-medium rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-200 disabled:opacity-50 flex items-center justify-center">
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