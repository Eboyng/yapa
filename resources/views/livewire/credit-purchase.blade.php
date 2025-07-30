<div class="" x-data="walletApp()" x-init="init()">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
        <!-- Header Section -->
        <div class="text-white">
            <div class="max-w-lg mx-auto px-4 pt-6 pb-4">
                <!-- Flash Messages -->
                @if (session()->has('success') || session()->has('error') || session()->has('info'))
                    <div class="mb-4" x-show="showFlash" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-1 transform translate-y-0">
                        @if (session()->has('success'))
                            <div class="bg-green-100 border border-green-300 rounded-xl p-3 text-green-800 shadow-sm">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm font-medium">{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="bg-red-100 border border-red-300 rounded-xl p-3 text-red-800 shadow-sm">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm font-medium">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif
                        @if (session()->has('info'))
                            <div class="bg-blue-100 border border-blue-300 rounded-xl p-3 text-blue-800 shadow-sm">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm font-medium">{{ session('info') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Unified Wallet Balance Card -->
                <div class="bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 backdrop-blur-sm rounded-2xl p-6 mb-6 shadow-lg border border-white/10">
                    <!-- Main Balance -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="text-white/90 text-sm font-medium block">Total Available</span>
                                <span class="text-white/70 text-xs">Naira Wallet</span>
                            </div>
                        </div>
                        <button id="fundWalletBtn" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-semibold transition-all transform hover:scale-105 active:scale-95 border border-white/20">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Add Money</span>
                            </span>
                        </button>
                    </div>
                    
                    <div class="text-3xl md:text-4xl font-bold mb-6">₦{{ number_format($user->getNairaWallet()->balance, 2) }}</div>
                    
                    <!-- Other Wallets Row -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Credits Wallet -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                            <div class="flex items-center mb-2">
                                <div class="w-6 h-6 bg-amber-400/20 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <span class="text-white/80 text-xs font-medium">Credits</span>
                            </div>
                            <div class="text-lg font-bold text-white">{{ number_format($user->getCreditWallet()->balance) }}</div>
                            <button id="buyCreditBtn" class="text-amber-300 text-xs hover:text-amber-200 transition-colors mt-1">
                                Buy Credits →
                            </button>
                        </div>
                        
                        <!-- Earnings Wallet -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                            <div class="flex items-center mb-2">
                                <div class="w-6 h-6 bg-green-400/20 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-white/80 text-xs font-medium">Earnings</span>
                            </div>
                            <div class="text-lg font-bold text-white">₦{{ number_format($user->getEarningsWallet()->balance, 2) }}</div>
                            <button id="withdrawBtn" class="text-green-300 text-xs hover:text-green-200 transition-colors mt-1">
                                Withdraw →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-lg mx-auto px-4 -mt-2">
            <!-- Quick Actions Grid -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm p-6 mb-6 border border-white/20">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-3 gap-4">
                    <!-- Airtime -->
                    <button id="airtimeBtn" class="flex flex-col items-center p-4 rounded-xl hover:bg-gradient-to-br hover:from-purple-50 hover:to-indigo-50 transition-all group border border-transparent hover:border-purple-200 hover:shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-purple-700">Airtime</span>
                    </button>

                    <!-- Data -->
                    <button id="dataBtn" class="flex flex-col items-center p-4 rounded-xl hover:bg-gradient-to-br hover:from-indigo-50 hover:to-blue-50 transition-all group border border-transparent hover:border-indigo-200 hover:shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-700">Data</span>
                    </button>

                    <!-- Bills -->
                    <button class="flex flex-col items-center p-4 rounded-xl hover:bg-gradient-to-br hover:from-green-50 hover:to-emerald-50 transition-all group border border-transparent hover:border-green-200 hover:shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-green-700">Bills</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Credit Purchase Modal -->
        <div id="creditModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden opacity-0 transition-all duration-300">
            <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-lg scale-95 transition-all duration-300">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Purchase Credits</h3>
                    <button id="closeCreditModal" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Payment Method Selector -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button wire:click="$set('creditPaymentMethod', 'naira')" class="flex items-center justify-center p-3 rounded-xl border-2 text-sm font-medium transition-all {{ $creditPaymentMethod === 'naira' ? 'bg-gradient-to-br from-orange-50 to-purple-50 border-orange-500 text-orange-700 shadow-md' : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-orange-300' }}">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                Naira Wallet
                            </button>
                            <button wire:click="$set('creditPaymentMethod', 'earnings')" class="flex items-center justify-center p-3 rounded-xl border-2 text-sm font-medium transition-all {{ $creditPaymentMethod === 'earnings' ? 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-500 text-green-700 shadow-md' : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-green-300' }}">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                Earnings
                            </button>
                        </div>
                    </div>

                    <!-- Credit Package Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Choose Package</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($creditPackages as $index => $package)
                                <div class="relative">
                                    <input type="radio" wire:click="selectCreditPackage({{ $index }})" name="credit_package" id="credit-package-{{ $index }}" class="sr-only peer">
                                    <label for="credit-package-{{ $index }}" class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:scale-105 active:scale-95 {{ $selectedCreditPackage === $index ? 'border-orange-500 bg-gradient-to-br from-orange-50 to-purple-50 shadow-md' : 'border-gray-200 hover:border-orange-300 hover:shadow-sm' }}">
                                        @if ($package['bonus'] > 0)
                                            <div class="absolute -top-2 -right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-md">
                                                +{{ number_format($package['bonus']) }}
                                            </div>
                                        @endif
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-gray-900 mb-1">{{ number_format($package['credits']) }}</div>
                                            <div class="text-xs text-gray-500 mb-2">Credits</div>
                                            @if ($package['bonus'] > 0)
                                                <div class="text-xs text-green-600 font-medium mb-2">Total: {{ number_format($package['total_credits']) }}</div>
                                            @endif
                                            <div class="text-base font-bold text-orange-600">₦{{ number_format($package['amount']) }}</div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Custom Amount Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Or enter custom amount (Credits)</label>
                        <input type="number" wire:model.live="customCreditAmount" class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 text-sm py-3 px-4" placeholder="Minimum {{ number_format($minCreditAmount) }} credits" min="{{ $minCreditAmount }}">
                        @error('customCreditAmount')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        @if ($customCreditAmount && $customCreditAmount >= $minCreditAmount)
                            <div class="text-sm text-gray-600 mt-2 bg-gray-50 p-3 rounded-lg">Cost: ₦{{ number_format($customCreditAmount * ($pricingConfig['credit_price'] ?? 1), 2) }}</div>
                        @endif
                    </div>

                    <!-- Purchase Button -->
                    <div class="flex space-x-3">
                        <button id="cancelCreditPurchase" class="flex-1 bg-gray-100 text-gray-700 font-semibold py-3 px-4 rounded-xl hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="purchaseCredits" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-orange-500 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50">
                            <span wire:loading.remove wire:target="purchaseCredits">Purchase Credits</span>
                            <span wire:loading wire:target="purchaseCredits" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Airtime Purchase Modal -->
        <div id="airtimeModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden opacity-0 transition-all duration-300">
            <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-lg scale-95 transition-all duration-300">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Buy Airtime</h3>
                    <button id="closeAirtimeModal" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Payment Method Selector -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button wire:click="$set('airtimePaymentMethod', 'naira')" class="flex items-center justify-center p-3 rounded-xl border-2 text-sm font-medium transition-all {{ $airtimePaymentMethod === 'naira' ? 'bg-gradient-to-br from-orange-50 to-purple-50 border-orange-500 text-orange-700 shadow-md' : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-orange-300' }}">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                Naira Wallet
                            </button>
                            <button wire:click="$set('airtimePaymentMethod', 'earnings')" class="flex items-center justify-center p-3 rounded-xl border-2 text-sm font-medium transition-all {{ $airtimePaymentMethod === 'earnings' ? 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-500 text-green-700 shadow-md' : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-green-300' }}">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                Earnings
                            </button>
                        </div>
                    </div>

                    <!-- Phone Number Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-gray-300 bg-gray-50 text-gray-600 text-sm font-medium">+234</span>
                            <input type="tel" wire:model.live="airtimePhoneNumber" class="flex-1 rounded-r-xl border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 text-sm py-3 px-4" placeholder="8012345678" maxlength="10">
                        </div>
                        @error('airtimePhoneNumber')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        @if ($detectedAirtimeNetwork)
                            <div class="text-sm text-blue-700 bg-blue-50 p-3 rounded-lg mt-2 border border-blue-200">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Network: {{ $detectedAirtimeNetwork }}
                            </div>
                        @endif
                    </div>

                    <!-- Amount Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">₦</span>
                            <input type="number" wire:model.live="airtimeAmount" class="w-full pl-8 pr-4 py-3 rounded-xl border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 text-sm" placeholder="Enter amount" min="50" max="5000">
                        </div>
                        @error('airtimeAmount')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        <div class="text-sm text-gray-500 mt-2 bg-gray-50 p-3 rounded-lg">Min: ₦50, Max: ₦5,000</div>
                    </div>

                    <!-- Purchase Button -->
                    <div class="flex space-x-3">
                        <button id="cancelAirtimePurchase" class="flex-1 bg-gray-100 text-gray-700 font-semibold py-3 px-4 rounded-xl hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="purchaseAirtime" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50">
                            <span wire:loading.remove wire:target="purchaseAirtime">Buy Airtime</span>
                            <span wire:loading wire:target="purchaseAirtime" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Purchase Modal -->
        <div id="dataModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden opacity-0 transition-all duration-300">
            <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-lg scale-95 transition-all duration-300">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Buy Data</h3>
                    <button id="closeDataModal" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Payment Method Selector -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button wire:click="$set('dataPaymentMethod', 'naira')" class="flex items-center justify-center p-3 rounded-xl border-2 text-sm font-medium transition-all {{ $dataPaymentMethod === 'naira' ? 'bg-gradient-to-br from-orange-50 to-purple-50 border-orange-500 text-orange-700 shadow-md' : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-orange-300' }}">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                Naira Wallet
                            </button>
                            <button wire:click="$set('dataPaymentMethod', 'earnings')" class="flex items-center justify-center p-3 rounded-xl border-2 text-sm font-medium transition-all {{ $dataPaymentMethod === 'earnings' ? 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-500 text-green-700 shadow-md' : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-green-300' }}">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                Earnings
                            </button>
                        </div>
                    </div>

                    <!-- Phone Number Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-gray-300 bg-gray-50 text-gray-600 text-sm font-medium">+234</span>
                            <input type="tel" wire:model.live="dataPhoneNumber" class="flex-1 rounded-r-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm py-3 px-4" placeholder="8012345678" maxlength="10">
                        </div>
                        @error('dataPhoneNumber')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        @if ($detectedDataNetwork)
                            <div class="text-sm text-blue-700 bg-blue-50 p-3 rounded-lg mt-2 border border-blue-200">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Network: {{ $detectedDataNetwork }}
                            </div>
                        @endif
                    </div>

                    <!-- Data Plan Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Data Plan</label>
                        <select wire:model.live="selectedDataPlan" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm py-3 px-4">
                            <option value="">Choose a plan</option>
                            @foreach ($availableDataPlans as $plan)
                                <option value="{{ $plan['plan_id'] }}">{{ $plan['size'] }} - {{ $plan['validity'] }} - ₦{{ number_format($plan['price'], 2) }}</option>
                            @endforeach
                        </select>
                        @error('selectedDataPlan')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Purchase Button -->
                    <div class="flex space-x-3">
                        <button id="cancelDataPurchase" class="flex-1 bg-gray-100 text-gray-700 font-semibold py-3 px-4 rounded-xl hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="purchaseData" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-indigo-500 to-blue-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50">
                            <span wire:loading.remove wire:target="purchaseData">Buy Data</span>
                            <span wire:loading wire:target="purchaseData" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fund Wallet Modal -->
        <div id="fundModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden opacity-0 transition-all duration-300">
            <div class="bg-white rounded-2xl w-full max-w-md max-h-[80vh] overflow-y-auto shadow-lg scale-95 transition-all duration-300">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Fund Naira Wallet</h3>
                    <button id="closeFundModal" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Amount Field -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount (₦)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">₦</span>
                            <input type="number" wire:model.live="fundAmount" class="w-full pl-8 pr-4 py-3 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" placeholder="Enter amount to fund" min="{{ $pricingConfig['minimum_amount'] ?? 100 }}">
                        </div>
                        @error('fundAmount')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        <div class="text-sm text-gray-500 mt-2 bg-gray-50 p-3 rounded-lg">Minimum: ₦{{ number_format($pricingConfig['minimum_amount'] ?? 100) }}</div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-blue-50 rounded-xl p-4 mb-6 border border-blue-200">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">Payment via Paystack</p>
                                <p class="text-xs">You will be redirected to Paystack to complete your payment securely.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex space-x-3">
                        <button id="cancelFundWallet" class="flex-1 bg-gray-100 text-gray-700 font-semibold py-3 px-4 rounded-xl hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="fundWallet" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all active:scale-95 disabled:opacity-50">
                            <span wire:loading.remove wire:target="fundWallet">Proceed to Payment</span>
                            <span wire:loading wire:target="fundWallet" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdrawal Modal (Keep existing Livewire functionality) -->
        <div x-show="$wire.showWithdrawModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50" @click.self="$wire.closeWithdrawModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-1 transform scale-100" class="bg-white rounded-2xl w-full max-w-md max-h-[80vh] overflow-y-auto shadow-lg">
                <!-- Modal Header -->
                <div class="flex justify-between items-center p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Withdraw Earnings</h3>
                    <button wire:click="closeWithdrawModal" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Withdrawal Methods -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center p-3 border-2 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer {{ $withdrawalMethod === 'bank_account' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}" @click="$wire.set('withdrawalMethod', 'bank_account')">
                            <input type="radio" wire:model.live="withdrawalMethod" value="bank_account" class="mr-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span class="font-medium">Bank Transfer</span>
                            </div>
                        </div>

                        <div class="flex items-center p-3 border-2 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer {{ $withdrawalMethod === 'palmpay' ? 'border-green-500 bg-green-50' : 'border-gray-200' }}" @click="$wire.set('withdrawalMethod', 'palmpay')">
                            <input type="radio" wire:model.live="withdrawalMethod" value="palmpay" class="mr-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span class="font-medium">PalmPay</span>
                            </div>
                        </div>

                        @if ($airtimeApiEnabled)
                            <div class="flex items-center p-3 border-2 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer {{ $withdrawalMethod === 'airtime' ? 'border-purple-500 bg-purple-50' : 'border-gray-200' }}" @click="$wire.set('withdrawalMethod', 'airtime')">
                                <input type="radio" wire:model.live="withdrawalMethod" value="airtime" class="mr-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                                    </svg>
                                    <span class="font-medium">Airtime</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Method-specific Fields -->
                    @if ($withdrawalMethod === 'bank_account')
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bank</label>
                                <select wire:model.live="bankCode" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4">
                                    <option value="">Select Bank</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('bankCode')
                                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                                <input type="text" wire:model.live="accountNumber" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4" placeholder="Enter account number" maxlength="10">
                                @error('accountNumber')
                                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($accountName)
                                <div class="text-sm text-green-700 bg-green-50 p-3 rounded-xl border border-green-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $accountName }}
                                </div>
                            @endif
                        </div>
                    @elseif($withdrawalMethod === 'palmpay')
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">PalmPay Number</label>
                            <input type="tel" wire:model.live="palmpayNumber" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4" placeholder="Enter PalmPay phone number" maxlength="11">
                            @error('palmpayNumber')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @elseif($withdrawalMethod === 'airtime')
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" wire:model.live="airtimeNumber" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4" placeholder="Enter phone number" maxlength="11">
                                @error('airtimeNumber')
                                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($detectedNetwork)
                                <div class="text-sm text-blue-700 bg-blue-50 p-3 rounded-xl border border-blue-200">Detected: {{ $detectedNetwork }}</div>
                            @endif
                            @if ($networks && count($networks) > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Network</label>
                                    <select wire:model.live="airtimeNetwork" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4">
                                        <option value="">Select Network</option>
                                        @foreach ($networks as $networkId => $networkName)
                                            <option value="{{ $networkId }}">{{ $networkName }}</option>
                                        @endforeach
                                    </select>
                                    @error('airtimeNetwork')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Amount Field -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount (₦)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">₦</span>
                            <input type="number" wire:model.live="amount" class="w-full pl-8 pr-4 py-3 rounded-xl border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200" placeholder="Enter amount" min="1000">
                        </div>
                        @error('amount')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        @if (isset($fees['total']) && $fees['total'] > 0)
                            <div class="bg-gray-50 p-3 rounded-lg mt-2 space-y-1">
                                <div class="text-sm text-gray-600">Fee: ₦{{ number_format($fees['total'], 2) }}</div>
                                <div class="text-sm font-medium text-gray-900">You'll receive: ₦{{ number_format($netAmount, 2) }}</div>
                            </div>
                        @endif
                        <div class="text-sm text-gray-500 mt-2">Minimum: ₦1,000</div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex space-x-3">
                        <button wire:click="closeWithdrawModal" class="flex-1 bg-gray-100 text-gray-700 font-semibold py-3 px-4 rounded-xl hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="processWithdrawal" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-green-500 to-teal-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all active:scale-95 disabled:opacity-50">
                            <span wire:loading.remove wire:target="processWithdrawal">Withdraw</span>
                            <span wire:loading wire:target="processWithdrawal" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Custom animations and responsive styles */
            .transition-all {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            /* Smooth scrolling for mobile */
            .overflow-y-auto {
                -webkit-overflow-scrolling: touch;
            }
            
            /* Custom scrollbar */
            .overflow-y-auto::-webkit-scrollbar {
                width: 4px;
            }
            
            .overflow-y-auto::-webkit-scrollbar-track {
                background: #f8fafc;
                border-radius: 2px;
            }
            
            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 2px;
            }
            
            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
            
            /* Enhanced button effects */
            .active\:scale-95:active {
                transform: scale(0.95);
            }
            
            .active\:scale-98:active {
                transform: scale(0.98);
            }
            
            /* Backdrop blur fallback */
            @supports not (backdrop-filter: blur(12px)) {
                .backdrop-blur-sm {
                    background-color: rgba(255, 255, 255, 0.95);
                }
            }

            /* Glass morphism effects */
            .bg-white\/80 {
                background-color: rgba(255, 255, 255, 0.8);
            }
            
            .bg-white\/10 {
                background-color: rgba(255, 255, 255, 0.1);
            }
            
            .bg-white\/20 {
                background-color: rgba(255, 255, 255, 0.2);
            }

            /* Modal show/hide classes */
            .modal-show {
                display: flex !important;
                opacity: 1 !important;
            }
            
            .modal-show > div {
                transform: scale(1) !important;
            }

            /* Mobile responsive enhancements */
            @media (max-width: 640px) {
                .text-3xl {
                    font-size: 1.875rem;
                    line-height: 2.25rem;
                }
                
                .p-6 {
                    padding: 1rem;
                }
                
                .px-6 {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }
            }

            /* Hover enhancements for desktop */
            @media (min-width: 768px) {
                .group:hover .group-hover\:scale-110 {
                    transform: scale(1.1);
                }
                
                .hover\:scale-105:hover {
                    transform: scale(1.05);
                }
                
                .hover\:scale-\[1\.02\]:hover {
                    transform: scale(1.02);
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://js.paystack.co/v1/inline.js"></script>
        <script>
            // Pure JavaScript Modal Management
            class ModalManager {
                constructor() {
                    this.init();
                }

                init() {
                    // Modal elements
                    this.modals = {
                        credit: document.getElementById('creditModal'),
                        airtime: document.getElementById('airtimeModal'),
                        data: document.getElementById('dataModal'),
                        fund: document.getElementById('fundModal')
                    };

                    // Button elements
                    this.buttons = {
                        buyCredit: document.getElementById('buyCreditBtn'),
                        airtime: document.getElementById('airtimeBtn'),
                        data: document.getElementById('dataBtn'),
                        fundWallet: document.getElementById('fundWalletBtn'),
                        withdraw: document.getElementById('withdrawBtn')
                    };

                    // Close button elements
                    this.closeButtons = {
                        credit: document.getElementById('closeCreditModal'),
                        airtime: document.getElementById('closeAirtimeModal'),
                        data: document.getElementById('closeDataModal'),
                        fund: document.getElementById('closeFundModal')
                    };

                    // Cancel button elements
                    this.cancelButtons = {
                        credit: document.getElementById('cancelCreditPurchase'),
                        airtime: document.getElementById('cancelAirtimePurchase'),
                        data: document.getElementById('cancelDataPurchase'),
                        fund: document.getElementById('cancelFundWallet')
                    };

                    this.setupEventListeners();
                }

                setupEventListeners() {
                    // Open modal buttons
                    if (this.buttons.buyCredit) {
                        this.buttons.buyCredit.addEventListener('click', () => this.openModal('credit'));
                    }
                    
                    if (this.buttons.airtime) {
                        this.buttons.airtime.addEventListener('click', () => this.openModal('airtime'));
                    }
                    
                    if (this.buttons.data) {
                        this.buttons.data.addEventListener('click', () => this.openModal('data'));
                    }
                    
                    if (this.buttons.fundWallet) {
                        this.buttons.fundWallet.addEventListener('click', () => this.openModal('fund'));
                    }

                    // Withdraw button triggers Livewire (separate from modal system)
                    if (this.buttons.withdraw) {
                        this.buttons.withdraw.addEventListener('click', () => {
                            window.Livewire.dispatch('openWithdrawModal');
                        });
                    }

                    // Close button events
                    Object.keys(this.closeButtons).forEach(modalType => {
                        if (this.closeButtons[modalType]) {
                            this.closeButtons[modalType].addEventListener('click', () => this.closeModal(modalType));
                        }
                    });

                    // Cancel button events
                    Object.keys(this.cancelButtons).forEach(modalType => {
                        if (this.cancelButtons[modalType]) {
                            this.cancelButtons[modalType].addEventListener('click', () => this.closeModal(modalType));
                        }
                    });

                    // Click outside to close
                    Object.keys(this.modals).forEach(modalType => {
                        if (this.modals[modalType]) {
                            this.modals[modalType].addEventListener('click', (e) => {
                                if (e.target === this.modals[modalType]) {
                                    this.closeModal(modalType);
                                }
                            });
                        }
                    });

                    // Escape key to close modals
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            this.closeAllModals();
                        }
                    });
                }

                openModal(type) {
                    const modal = this.modals[type];
                    if (modal) {
                        // Close any open modals first
                        this.closeAllModals();
                        
                        // Show modal
                        modal.classList.remove('hidden');
                        
                        // Trigger animation
                        setTimeout(() => {
                            modal.classList.add('modal-show');
                        }, 10);

                        // Add haptic feedback for mobile
                        if ('vibrate' in navigator) {
                            navigator.vibrate(30);
                        }

                        // Prevent body scroll
                        document.body.style.overflow = 'hidden';
                    }
                }

                closeModal(type) {
                    const modal = this.modals[type];
                    if (modal) {
                        modal.classList.remove('modal-show');
                        
                        setTimeout(() => {
                            modal.classList.add('hidden');
                            document.body.style.overflow = '';
                        }, 300);
                    }
                }

                closeAllModals() {
                    Object.keys(this.modals).forEach(modalType => {
                        this.closeModal(modalType);
                    });
                }
            }

            function walletApp() {
                return {
                    showFlash: true,
                    
                    init() {
                        // Initialize modal manager
                        new ModalManager();
                        
                        // Auto-hide flash messages after 5 seconds
                        if (this.showFlash) {
                            setTimeout(() => {
                                this.showFlash = false;
                            }, 5000);
                        }
                        
                        // Add haptic feedback for supported devices
                        if ('vibrate' in navigator) {
                            document.addEventListener('click', function(e) {
                                if (e.target.closest('button')) {
                                    navigator.vibrate(30);
                                }
                            });
                        }
                    }
                }
            }
            
            document.addEventListener('livewire:init', () => {
                Livewire.on('initiate-paystack-payment', (event) => {
                    const handler = PaystackPop.setup({
                        key: '{{ $paystackPublicKey }}',
                        email: '{{ $user->email }}',
                        amount: event.amount * 100,
                        currency: 'NGN',
                        ref: event.reference,
                        metadata: {
                            custom_fields: [{
                                display_name: "Transaction Type",
                                variable_name: "transaction_type",
                                value: "{{ $nairaFundingCategory }}"
                            }]
                        },
                        callback: function(response) {
                            Livewire.dispatch('payment-successful', {
                                reference: response.reference,
                                transaction: response.transaction
                            });
                        },
                        onClose: function() {
                            Livewire.dispatch('payment-cancelled');
                        }
                    });
                    handler.openIframe();
                });
            });
        </script>
    @endpush
</div>