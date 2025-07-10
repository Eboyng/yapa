<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">Purchase Channel</h2>
            <p class="text-gray-600 mt-2">Review the details and confirm your purchase</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Channel Details -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Channel Details</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Channel Name:</span>
                            <span class="font-semibold">{{ $channelSale->channel_name }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">WhatsApp Number:</span>
                            <span class="font-semibold">{{ $channelSale->whatsapp_number }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Category:</span>
                            <span class="font-semibold">{{ $channelSale->categoryLabel }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Audience Size:</span>
                            <span class="font-semibold">{{ $channelSale->formattedAudienceSize }}</span>
                        </div>
                        
                        @if($channelSale->engagement_rate)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Engagement Rate:</span>
                                <span class="font-semibold">{{ $channelSale->engagement_rate }}%</span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Seller:</span>
                            <span class="font-semibold">{{ $channelSale->user->name }}</span>
                        </div>
                        
                        <div class="flex justify-between border-t pt-3">
                            <span class="text-gray-600 text-lg">Price:</span>
                            <span class="font-bold text-2xl text-green-600">{{ $channelSale->formattedPrice }}</span>
                        </div>
                    </div>
                    
                    @if($channelSale->description)
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-900 mb-2">Description</h4>
                            <p class="text-gray-700 bg-gray-50 rounded-lg p-4">{{ $channelSale->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Purchase Form -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase Information</h3>
                    
                    <form wire:submit.prevent="showConfirmationModal">
                        <!-- Buyer Note -->
                        <div class="mb-6">
                            <label for="buyerNote" class="block text-sm font-medium text-gray-700 mb-2">
                                Note to Seller (Optional)
                            </label>
                            <textarea id="buyerNote" wire:model="buyerNote" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Any questions or special requests for the seller..."></textarea>
                            @error('buyerNote') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Terms Agreement -->
                        <div class="mb-6">
                            <label class="flex items-start">
                                <input type="checkbox" wire:model="agreedToTerms" 
                                       class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">
                                    I agree to the <a href="#" class="text-blue-600 hover:underline">terms and conditions</a> 
                                    and understand that funds will be held in escrow until I confirm receipt of the channel.
                                </span>
                            </label>
                            @error('agreedToTerms') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Wallet Balance Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold text-blue-900 mb-2">Payment Information</h4>
                            <div class="space-y-2 text-sm">
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

                        <!-- Action Buttons -->
                        <div class="flex space-x-4">
                            <a href="{{ route('channel-sale.browse') }}"
                               class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors text-center">
                                Back to Browse
                            </a>
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50"
                                    @if(auth()->user()->getNairaWallet()->balance < $channelSale->price) disabled @endif>
                                @if(auth()->user()->getNairaWallet()->balance < $channelSale->price)
                                    Insufficient Balance
                                @else
                                    Purchase Channel
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmation)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="cancelPurchase">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirm Purchase</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to purchase "{{ $channelSale->channel_name }}" for {{ $channelSale->formattedPrice }}?
                            This amount will be held in escrow until you confirm receipt.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <div class="flex space-x-3">
                            <button wire:click="cancelPurchase"
                                    class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400 transition-colors">
                                Cancel
                            </button>
                            <button wire:click="confirmPurchase" wire:loading.attr="disabled"
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50">
                                <span wire:loading.remove>Confirm</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>