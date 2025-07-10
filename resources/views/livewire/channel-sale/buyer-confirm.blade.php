<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">Confirm Channel Receipt</h2>
            <p class="text-gray-600 mt-2">Please confirm that you have received access to the channel</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Purchase Details -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase Details</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Channel Name:</span>
                            <span class="font-semibold">{{ $purchase->channelSale->channel_name }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">WhatsApp Number:</span>
                            <span class="font-semibold">{{ $purchase->channelSale->whatsapp_number }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Category:</span>
                            <span class="font-semibold">{{ $purchase->channelSale->categoryLabel }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Audience Size:</span>
                            <span class="font-semibold">{{ $purchase->channelSale->formattedAudienceSize }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Seller:</span>
                            <span class="font-semibold">{{ $purchase->channelSale->user->name }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Purchase Date:</span>
                            <span class="font-semibold">{{ $purchase->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <div class="flex justify-between border-t pt-3">
                            <span class="text-gray-600 text-lg">Amount Paid:</span>
                            <span class="font-bold text-2xl text-green-600">{{ $purchase->formattedPrice }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $purchase->statusColor }}">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </div>
                    </div>
                    
                    @if($purchase->channelSale->description)
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-900 mb-2">Channel Description</h4>
                            <p class="text-gray-700 bg-gray-50 rounded-lg p-4">{{ $purchase->channelSale->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Confirmation Form -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirmation</h3>
                    
                    <!-- Instructions -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-blue-900 mb-2">Before Confirming:</h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Verify you have admin access to the WhatsApp channel</li>
                            <li>• Check that the channel details match what was advertised</li>
                            <li>• Ensure the audience size is as described</li>
                            <li>• Test that you can post and manage the channel</li>
                        </ul>
                    </div>

                    <form wire:submit.prevent="showConfirmationModal">
                        <!-- Confirmation Note -->
                        <div class="mb-6">
                            <label for="confirmationNote" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmation Note (Optional)
                            </label>
                            <textarea id="confirmationNote" wire:model="confirmationNote" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Any feedback about the channel or transaction..."></textarea>
                            @error('confirmationNote') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Warning -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-yellow-800">Important Notice</h4>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        Once you confirm receipt, the funds will be released to the seller and this action cannot be undone.
                                        Only confirm if you have successfully received access to the channel.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="w-full px-4 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-semibold">
                                <i class="fas fa-check mr-2"></i>
                                Confirm Receipt & Release Payment
                            </button>
                            
                            <button type="button" wire:click="requestRefund"
                                    wire:confirm="Are you sure you want to request a refund? This will return the funds to your wallet."
                                    class="w-full px-4 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors font-semibold">
                                <i class="fas fa-undo mr-2"></i>
                                Request Refund
                            </button>
                            
                            <a href="{{ route('channel-sale.my-purchases') }}"
                               class="block w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors text-center">
                                Back to My Purchases
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmation)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="cancelConfirmation">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirm Receipt</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to confirm receipt of "{{ $purchase->channelSale->channel_name }}"?
                            This will release {{ $purchase->formattedPrice }} to the seller.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <div class="flex space-x-3">
                            <button wire:click="cancelConfirmation"
                                    class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400 transition-colors">
                                Cancel
                            </button>
                            <button wire:click="confirmReceived" wire:loading.attr="disabled"
                                    class="flex-1 px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md hover:bg-green-700 transition-colors disabled:opacity-50">
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