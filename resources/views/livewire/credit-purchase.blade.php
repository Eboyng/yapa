<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Purchase Credits</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Top up your account with credits to join batches and access premium features</p>
        
        @if($retryTransactionId)
            <div class="mt-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Retrying Failed Transaction</h3>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">You are retrying a previously failed payment. The amount has been pre-filled.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Current Balance -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium">Current Balance</h3>
                <p class="text-3xl font-bold mt-2">{{ number_format($user->credits_balance) }} Credits</p>
                <p class="text-blue-100 mt-1">≈ ₦{{ number_format($user->credits_balance * $pricingConfig['credit_price'], 2) }}</p>
            </div>
            <div class="text-right">
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <svg class="w-8 h-8 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Information -->
    <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-8">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Pricing Information</h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <p>• 1 Credit = ₦{{ number_format($pricingConfig['credit_price'], 2) }}</p>
                    <p>• Minimum purchase: {{ $pricingConfig['minimum_credits'] }} credits (₦{{ number_format($pricingConfig['minimum_amount']) }})</p>
                    <p>• Larger packages include bonus credits!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Selection -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Choose a Package</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @foreach($packages as $index => $package)
                <div class="relative">
                    <input type="radio" 
                           wire:click="selectPackage({{ $index }})" 
                           name="package" 
                           id="package-{{ $index }}" 
                           class="sr-only peer">
                    <label for="package-{{ $index }}" 
                           class="block p-4 border-2 rounded-lg cursor-pointer transition-all duration-200
                                  {{ $selectedPackage === $index ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500' }}">
                        
                        @if($package['bonus'] > 0)
                            <div class="absolute -top-2 -right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                +{{ $package['bonus'] }} Bonus
                            </div>
                        @endif
                        
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($package['credits']) }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Credits</div>
                            
                            @if($package['bonus'] > 0)
                                <div class="text-xs text-green-600 dark:text-green-400 font-medium mt-1">
                                    + {{ $package['bonus'] }} Bonus = {{ number_format($package['total_credits']) }} Total
                                </div>
                            @endif
                            
                            <div class="mt-3 text-xl font-semibold text-indigo-600 dark:text-indigo-400">
                                ₦{{ number_format($package['amount']) }}
                            </div>
                            
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                ₦{{ number_format($package['amount'] / $package['total_credits'], 2) }} per credit
                            </div>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>

        <!-- Custom Amount Section -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="flex items-center mb-4">
                <input type="radio" 
                       wire:click="selectPackage(-1)" 
                       name="package" 
                       id="package-custom" 
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600">
                <label for="package-custom" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Custom Amount
                </label>
            </div>
            
            @if($selectedPackage === -1)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="customAmount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Amount (₦)
                        </label>
                        <input wire:model.live.debounce.300ms="customAmount" 
                               type="number" 
                               id="customAmount" 
                               min="300" 
                               step="1"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               placeholder="Enter amount in Naira">
                        @error('customAmount')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="customCredits" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Credits
                        </label>
                        <input wire:model.live.debounce.300ms="customCredits" 
                               type="number" 
                               id="customCredits" 
                               min="100" 
                               step="1"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               placeholder="Enter number of credits">
                        @error('customCredits')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                @if($customAmount > 0 && $customCredits > 0)
                    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            You will receive <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($customCredits) }} credits</span> 
                            for <span class="font-semibold text-gray-900 dark:text-white">₦{{ number_format($customAmount) }}</span>
                            <span class="text-xs">(₦{{ number_format($customAmount / $customCredits, 2) }} per credit)</span>
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Payment Summary -->
    @if($selectedPackage >= 0 || ($selectedPackage === -1 && $customAmount > 0))
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payment Summary</h3>
            
            @php
                $amount = 0;
                $credits = 0;
                $bonus = 0;
                
                if ($selectedPackage >= 0 && isset($packages[$selectedPackage])) {
                    $package = $packages[$selectedPackage];
                    $amount = $package['amount'];
                    $credits = $package['credits'];
                    $bonus = $package['bonus'];
                } elseif ($selectedPackage === -1) {
                    $amount = $customAmount;
                    $credits = $customCredits;
                }
            @endphp
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Base Credits:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ number_format($credits) }}</span>
                </div>
                
                @if($bonus > 0)
                    <div class="flex justify-between">
                        <span class="text-green-600 dark:text-green-400">Bonus Credits:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">+{{ number_format($bonus) }}</span>
                    </div>
                @endif
                
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                    <div class="flex justify-between">
                        <span class="text-lg font-medium text-gray-900 dark:text-white">Total Credits:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($credits + $bonus) }}</span>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-lg font-medium text-gray-900 dark:text-white">Amount to Pay:</span>
                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">₦{{ number_format($amount) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Button -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
        <button wire:click="purchaseCredits" 
                @if($isProcessing || ($selectedPackage === -1 && (!$customAmount || !$customCredits)) || ($selectedPackage < 0 && $selectedPackage !== -1)) disabled @endif
                class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
            
            @if($isProcessing)
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            @else
                <svg class="-ml-1 mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                </svg>
                @if($retryTransactionId)
                    Retry Payment
                @else
                    Proceed to Payment
                @endif
            @endif
        </button>
        
        <div class="mt-4 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
            </svg>
            Secured by Paystack • SSL Encrypted
        </div>
        
        <div class="mt-2 text-center">
            <img src="https://paystack.com/assets/img/payments/paystack-payment-options.png" 
                 alt="Payment Options" 
                 class="h-8 mx-auto opacity-60">
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-8 bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Need Help?</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Payment Issues</h4>
                <p>If your payment fails, you can retry it from your transaction history. Failed transactions are kept for 24 hours.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Refunds</h4>
                <p>Credits are non-refundable once purchased. However, if a batch is cancelled by admin, you'll receive a full refund.</p>
            </div>
        </div>
    </div>
</div>