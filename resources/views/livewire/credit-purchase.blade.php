<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

        <!-- Flash Messages -->
        @if (session()->has('success') || session()->has('error') || session()->has('info'))
            <div class="mb-6 animate-slideDown">
                @if (session()->has('success'))
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if (session()->has('info'))
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Credit & Wallet</h1>
            <p class="text-gray-600 text-lg">Purchase credits or withdraw your earnings</p>
        </div>

        <!-- Balance Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Credit Balance -->
            <div class="bg-gradient-to-r from-orange-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium opacity-90">Credit Balance</h3>
                        <p class="text-3xl font-bold mt-2">{{ number_format($user->getCreditWallet()->balance) }}</p>
                        <p class="text-white/80 mt-1 text-sm">≈ ₦{{ number_format($user->getCreditWallet()->balance * $pricingConfig['credit_price'], 2) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Earnings Balance -->
            <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium opacity-90">Earnings Balance</h3>
                        <p class="text-3xl font-bold mt-2">₦{{ number_format($user->wallets()->where('type', 'earnings')->first()->balance ?? 0, 2) }}</p>
                        <p class="text-white/80 mt-1 text-sm">Available for withdrawal</p>
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 mb-8">
            <button class="flex-1 bg-gradient-to-r from-orange-500 to-purple-600 text-white font-semibold py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-orange-200">
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Purchase Credits
                </div>
            </button>
            
            <button wire:click="openWithdrawModal" class="flex-1 bg-gradient-to-r from-green-500 to-teal-600 text-white font-semibold py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-green-200">
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Withdraw Earnings
                </div>
            </button>
        </div>

        <!-- Credit Purchase Section -->
        <div class="bg-white shadow-sm rounded-2xl p-6 mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Purchase Credits
            </h3>

            <!-- Pricing Information -->
            <div class="bg-gradient-to-r from-orange-50 to-purple-50 border border-orange-200 rounded-xl p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-purple-500 rounded-xl flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h4 class="text-sm font-semibold text-gray-900">Pricing Information</h4>
                        <div class="mt-2 space-y-1 text-sm text-gray-700">
                            <div class="flex items-center">
                                <svg class="w-3 h-3 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                <span>1 Credit = ₦{{ number_format($pricingConfig['credit_price'], 2) }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                <span>Minimum purchase: {{ $pricingConfig['minimum_credits'] }} credits (₦{{ number_format($pricingConfig['minimum_amount']) }})</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                <span class="font-medium">Larger packages include bonus credits!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package Selection -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Choose a Package
            </h3>
            
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                @foreach($packages as $index => $package)
                    <div class="relative">
                        <input type="radio" 
                               wire:click="selectPackage({{ $index }})" 
                               name="package" 
                               id="package-{{ $index }}" 
                               class="sr-only peer">
                        <label for="package-{{ $index }}" 
                               class="block p-2 sm:p-4 border-2 rounded-xl sm:rounded-2xl cursor-pointer transition-all duration-300 transform hover:scale-105
                                      {{ $selectedPackage === $index ? 'border-orange-500 bg-gradient-to-br from-orange-50 to-purple-50 shadow-lg' : 'border-gray-200 hover:border-orange-300 hover:shadow-md' }}">
                            
                            @if($package['bonus'] > 0)
                                <div class="absolute -top-1 -right-1 sm:-top-2 sm:-right-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-xs font-bold px-1 py-1 sm:px-2 rounded-full shadow-lg animate-pulse">
                                    +{{ $package['bonus'] }} Bonus
                                </div>
                            @endif
                            
                            <div class="text-center">
                                <div class="text-lg sm:text-2xl lg:text-3xl font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">
                                    {{ number_format($package['credits']) }}
                                </div>
                                <div class="text-xs sm:text-sm text-gray-500">Credits</div>
                                
                                @if($package['bonus'] > 0)
                                    <div class="text-xs text-green-600 font-medium mt-1 sm:mt-2 bg-green-50 rounded-lg py-1 px-1">
                                        + {{ $package['bonus'] }} Bonus = {{ number_format($package['total_credits']) }} Total
                                    </div>
                                @endif
                                
                                <div class="mt-2 sm:mt-3 text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">
                                    ₦{{ number_format($package['amount']) }}
                                </div>
                                
                                <div class="text-xs text-gray-500 mt-1">
                                    ₦{{ number_format($package['amount'] / $package['total_credits'], 2) }} per credit
                                </div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            <!-- Custom Amount Section -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center mb-4">
                    <input type="radio" 
                           wire:click="selectPackage(-1)" 
                           name="package" 
                           id="package-custom" 
                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                    <label for="package-custom" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Custom Amount
                    </label>
                </div>
                
                @if($selectedPackage === -1)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl">
                        <div>
                            <label for="customAmount" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Amount (₦)
                            </label>
                            <input wire:model.live.debounce.300ms="customAmount" 
                                   type="number" 
                                   id="customAmount" 
                                   min="300" 
                                   step="1"
                                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200" 
                                   placeholder="Enter amount in Naira">
                            @error('customAmount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="customCredits" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                Credits
                            </label>
                            <input wire:model.live.debounce.300ms="customCredits" 
                                   type="number" 
                                   id="customCredits" 
                                   min="100" 
                                   step="1"
                                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200" 
                                   placeholder="Enter number of credits">
                            @error('customCredits')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    @if($customAmount > 0 && $customCredits > 0)
                        <div class="mt-4 p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-green-800">
                                    You will receive <span class="font-semibold">{{ number_format($customCredits) }} credits</span> 
                                    for <span class="font-semibold">₦{{ number_format($customAmount) }}</span>
                                    <span class="text-xs ml-1">(₦{{ number_format($customAmount / $customCredits, 2) }} per credit)</span>
                                </p>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Payment Summary -->
        @if($selectedPackage >= 0 || ($selectedPackage === -1 && $customAmount > 0))
            <div class="bg-white shadow-sm rounded-2xl p-4 sm:p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Payment Summary
                </h3>
                
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
                
                <div class="space-y-3 bg-gray-50 rounded-xl p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                            </svg>
                            Base Credits:
                        </span>
                        <span class="font-medium text-gray-900">{{ number_format($credits) }}</span>
                    </div>
                    
                    @if($bonus > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-green-600 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Bonus Credits:
                            </span>
                            <span class="font-medium text-green-600">+{{ number_format($bonus) }}</span>
                        </div>
                    @endif
                    
                    <div class="border-t border-gray-200 pt-3 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-900">Total Credits:</span>
                            <span class="text-lg font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">{{ number_format($credits + $bonus) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-900">Amount to Pay:</span>
                            <span class="text-lg font-bold text-orange-600">₦{{ number_format($amount) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Payment Button -->
        <div class="bg-white shadow-sm rounded-2xl p-4 sm:p-6 mb-8">
            <button wire:click="purchaseCredits" 
                    @if($isProcessing || ($selectedPackage === -1 && (!$customAmount || !$customCredits)) || ($selectedPackage < 0 && $selectedPackage !== -1)) disabled @endif
                    class="w-full flex justify-center items-center px-4 sm:px-6 py-3 sm:py-4 border border-transparent text-sm sm:text-base font-medium sm:font-semibold rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 disabled:hover:scale-100 shadow-lg hover:shadow-xl">
                
                @if($isProcessing)
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing Payment...
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
            
            <div class="mt-4 flex items-center justify-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                </svg>
                Secured by Paystack • SSL Encrypted
            </div>
            
            <div class="mt-4 text-center">
                <div class="inline-flex items-center space-x-2 bg-gray-50 rounded-lg px-3 py-2">
                    <span class="text-xs text-gray-500">Powered by</span>
                    <svg class="h-4 w-auto text-green-600" viewBox="0 0 100 24" fill="currentColor">
                        <path d="M15.16 6.27c-2.05 0-3.86.84-5.16 2.2V6.51H5.84v17.33H10V17.6c1.3 1.36 3.11 2.2 5.16 2.2 4.36 0 7.9-3.54 7.9-7.9s-3.54-7.9-7.9-7.9zm-.7 12.05c-2.3 0-4.15-1.86-4.15-4.15s1.85-4.15 4.15-4.15 4.15 1.86 4.15 4.15-1.85 4.15-4.15 4.15zM45.56 6.27c-4.36 0-7.9 3.54-7.9 7.9s3.54 7.9 7.9 7.9c4.36 0 7.9-3.54 7.9-7.9s-3.54-7.9-7.9-7.9zm0 12.05c-2.3 0-4.15-1.86-4.15-4.15s1.85-4.15 4.15-4.15 4.15 1.86 4.15 4.15-1.85 4.15-4.15 4.15zM69.6 14.27l4.68-7.76h-4.68l-2.42 4.15-2.42-4.15h-4.68l4.68 7.76-5.22 8.57h4.68l2.96-4.84 2.96 4.84h4.68l-5.22-8.57zM97.7 6.27c-1.84 0-3.5.63-4.84 1.68V2.17h-4.16v20.67h4.16v-9.48c0-1.84 1.3-3.33 2.9-3.33s2.9 1.49 2.9 3.33v9.48H102V13.2c0-3.94-2.89-6.93-4.3-6.93z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Need Help?
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600">
                <div class="bg-white rounded-xl p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <h4 class="font-medium text-gray-900">Payment Issues</h4>
                    </div>
                    <p>If your payment fails, you can retry it from your transaction history. Failed transactions are kept for 24 hours.</p>
                </div>
                <div class="bg-white rounded-xl p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        <h4 class="font-medium text-gray-900">Refunds</h4>
                    </div>
                    <p>Credits are non-refundable once purchased. However, if a batch is cancelled by admin, you'll receive a full refund.</p>
                </div>
            </div>
        </div>
    </div>

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
    
    /* Custom radio button styling */
    input[type="radio"]:checked + label {
        transform: scale(1.02);
    }
    
    /* Custom number input styling */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    input[type="number"] {
        -moz-appearance: textfield;
    }
    
    /* Pulse animation for bonus badges */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .8;
        }
    }
    
    /* Custom scrollbar */
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

<script>
    // Add smooth scrolling behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Add intersection observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe elements for scroll animations
    document.addEventListener('DOMContentLoaded', function() {
        const animatedElements = document.querySelectorAll('.bg-white, .bg-gradient-to-r');
        animatedElements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            element.style.transition = `opacity 0.6s ease-out ${index * 0.1}s, transform 0.6s ease-out ${index * 0.1}s`;
            observer.observe(element);
        });
    });
    
    // Enhanced package selection animation
    document.addEventListener('change', function(e) {
        if (e.target.type === 'radio' && e.target.name === 'package') {
            const allLabels = document.querySelectorAll('label[for^="package-"]');
            allLabels.forEach(label => {
                label.style.transform = 'scale(1)';
                label.style.transition = 'transform 0.2s ease-out';
            });
            
            const selectedLabel = document.querySelector(`label[for="${e.target.id}"]`);
            if (selectedLabel) {
                selectedLabel.style.transform = 'scale(1.02)';
            }
        }
    });
    
    // Add hover effects for package cards
    document.addEventListener('DOMContentLoaded', function() {
        const packageLabels = document.querySelectorAll('label[for^="package-"]');
        packageLabels.forEach(label => {
            label.addEventListener('mouseenter', function() {
                if (!this.querySelector('input').checked) {
                    this.style.transform = 'scale(1.02)';
                }
            });
            
            label.addEventListener('mouseleave', function() {
                if (!this.querySelector('input').checked) {
                    this.style.transform = 'scale(1)';
                }
            });
        });
    });
    
    // Add focus animations for form inputs
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('input[type="number"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.2s ease-out';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    });
    
    // Add click ripple effect for main button
    document.addEventListener('DOMContentLoaded', function() {
        const mainButton = document.querySelector('button[wire\\:click="purchaseCredits"]');
        if (mainButton) {
            mainButton.addEventListener('click', function(e) {
                if (this.disabled) return;
                
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.5);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                `;
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        }
    });
    
    // Add CSS for ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
</script>
</div>

