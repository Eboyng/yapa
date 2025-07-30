<div class="" x-data="walletApp()" x-init="init()">
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-orange-50">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-orange-600 text-white">
            <div class="max-w-6xl mx-auto px-6 py-12">
                <!-- Flash Messages -->
                @if (session()->has('success') || session()->has('error') || session()->has('info'))
                    <div class="mb-8" x-show="showFlash" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform -translate-y-6" x-transition:enter-end="opacity-1 transform translate-y-0">
                        @if (session()->has('success'))
                            <div class="bg-white/10 backdrop-blur-lg text-white p-6 rounded-2xl border border-white/20 shadow-2xl">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-lg">{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="bg-white/10 backdrop-blur-lg text-white p-6 rounded-2xl border border-white/20 shadow-2xl">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center mr-4">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-lg">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif
                        @if (session()->has('info'))
                            <div class="bg-white/10 backdrop-blur-lg text-white p-6 rounded-2xl border border-white/20 shadow-2xl">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-lg">{{ session('info') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Main Wallet Card -->
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl rounded-3xl p-8 mb-8 hover:bg-white/15 transition-all duration-500">
                    <!-- Main Balance Header -->
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-purple-500 rounded-2xl flex items-center justify-center mr-6 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-sm font-bold text-white/80 uppercase tracking-wider mb-1">Naira Wallet</h1>
                                <p class="text-xs text-white/60 uppercase tracking-wide">Total Available</p>
                            </div>
                        </div>
                        <button wire:click="openFundModal" class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-4 font-bold rounded-2xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300" wire:loading.attr="disabled">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span wire:loading.remove wire:target="openFundModal">Add Money</span>
                                <span wire:loading wire:target="openFundModal">Loading...</span>
                            </span>
                        </button>
                    </div>
                    
                    <div class="text-6xl font-bold mb-8 text-white tracking-tight">₦{{ number_format($user->getNairaWallet()->balance, 2) }}</div>
                    
                    <!-- Other Wallets Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Credits Wallet -->
                        <div class="bg-gradient-to-br from-orange-400 to-orange-500 text-white rounded-2xl p-6 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <span class="font-bold">Credits</span>
                            </div>
                            <div class="text-3xl font-bold mb-4">{{ number_format($user->getCreditWallet()->balance) }}</div>
                            <button wire:click="openCreditModal" class="bg-white/20 hover:bg-white/30 backdrop-blur text-white px-6 py-3 font-semibold rounded-xl text-sm transition-all duration-300 flex items-center" wire:loading.attr="disabled">
                                Buy Credits
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Earnings Wallet -->
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-2xl p-6 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="font-bold">Earnings</span>
                            </div>
                            <div class="text-3xl font-bold mb-4">₦{{ number_format($user->getEarningsWallet()->balance, 2) }}</div>
                            <button wire:click="openWithdrawModal" class="bg-white/20 hover:bg-white/30 backdrop-blur text-white px-6 py-3 font-semibold rounded-xl text-sm transition-all duration-300 flex items-center" wire:loading.attr="disabled">
                                Withdraw
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-6xl mx-auto px-6 py-12">
            <!-- Quick Actions Grid -->
            <div class="bg-white rounded-3xl shadow-xl p-8 mb-8 border border-gray-100">
                <h2 class="text-3xl font-bold text-gray-800 mb-8">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Airtime -->
                    <button wire:click="openAirtimeModal" class="group bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-2xl p-8 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300" wire:loading.attr="disabled">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-white/30 transition-all duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 717.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                            </svg>
                        </div>
                        <span class="text-lg">Airtime</span>
                    </button>

                    <!-- Data -->
                    <button wire:click="openDataModal" class="group bg-gradient-to-br from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-2xl p-8 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300" wire:loading.attr="disabled">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-white/30 transition-all duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span class="text-lg">Data</span>
                    </button>

                    <!-- Bills -->
                    <button class="group bg-gradient-to-br from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-2xl p-8 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-white/30 transition-all duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="text-lg">Bills</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Fund Wallet Modal -->
        <div x-show="$wire.showFundModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50" @click.self="$wire.closeFundModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-1 transform scale-100" class="bg-white rounded-3xl shadow-2xl w-full max-w-md max-h-[80vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-purple-600 to-orange-600 text-white p-6 rounded-t-3xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold">Fund Wallet</h3>
                        <button wire:click="closeFundModal" class="text-white/80 hover:text-white transform hover:scale-110 transition-all duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Amount Field -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Amount (₦)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">₦</span>
                            <input type="number" wire:model.live="fundAmount" class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl font-semibold text-lg focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200" placeholder="Enter amount" min="{{ $pricingConfig['minimum_amount'] ?? 100 }}">
                        </div>
                        @error('fundAmount')
                            <span class="text-red-500 text-sm font-medium mt-2 block">{{ $message }}</span>
                        @enderror
                        <div class="text-xs font-medium mt-2 bg-gray-50 p-3 rounded-lg text-gray-600">Minimum: ₦{{ number_format($pricingConfig['minimum_amount'] ?? 100) }}</div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                        <div class="flex items-start">
                            <div class="w-6 h-6 text-blue-600 mt-1 mr-3">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="text-sm">
                                <p class="font-semibold text-blue-800 mb-1">Payment via Paystack</p>
                                <p class="text-blue-600 text-xs">Secure payment processing</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex space-x-4">
                        <button wire:click="closeFundModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 px-4 rounded-xl transition-all duration-200">
                            Cancel
                        </button>
                        <button wire:click="fundWallet" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-purple-600 to-orange-600 hover:from-purple-700 hover:to-orange-700 text-white font-semibold py-4 px-4 rounded-xl disabled:opacity-50 transition-all duration-200">
                            <span wire:loading.remove wire:target="fundWallet">Proceed</span>
                            <span wire:loading wire:target="fundWallet" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
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

        <!-- Credit Purchase Modal -->
        <div x-show="$wire.showCreditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50" @click.self="$wire.closeCreditModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-1 transform scale-100" class="bg-white rounded-3xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-t-3xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold">Buy Credits</h3>
                        <button wire:click="closeCreditModal" class="text-white/80 hover:text-white transform hover:scale-110 transition-all duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Payment Method Selector -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Payment Method</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button wire:click="$set('creditPaymentMethod', 'naira')" class="flex items-center justify-center p-4 border-2 rounded-xl font-semibold text-sm transition-all duration-200 {{ $creditPaymentMethod === 'naira' ? 'border-orange-500 bg-orange-50 text-orange-700' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                Naira
                            </button>
                            <button wire:click="$set('creditPaymentMethod', 'earnings')" class="flex items-center justify-center p-4 border-2 rounded-xl font-semibold text-sm transition-all duration-200 {{ $creditPaymentMethod === 'earnings' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                Earnings
                            </button>
                        </div>
                    </div>

                    <!-- Credit Package Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Choose Package</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($creditPackages as $index => $package)
                                <div class="relative">
                                    <input type="radio" wire:click="selectCreditPackage({{ $index }})" name="credit_package" id="credit-package-{{ $index }}" class="sr-only peer">
                                    <label for="credit-package-{{ $index }}" class="block p-4 border-2 cursor-pointer font-semibold text-center rounded-xl transition-all duration-200 hover:border-orange-300 {{ $selectedCreditPackage === $index ? 'border-orange-500 bg-orange-50 text-orange-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                        @if ($package['bonus'] > 0)
                                            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                                +{{ number_format($package['bonus']) }}
                                            </div>
                                        @endif
                                        <div class="text-lg font-bold mb-1">{{ number_format($package['credits']) }}</div>
                                        <div class="text-xs mb-2 text-gray-500">Credits</div>
                                        @if ($package['bonus'] > 0)
                                            <div class="text-xs text-green-600 font-semibold mb-2">Total: {{ number_format($package['total_credits']) }}</div>
                                        @endif
                                        <div class="text-base font-bold">₦{{ number_format($package['amount']) }}</div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Custom Amount Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Amount (Credits)</label>
                        <input type="number" wire:model.live="customCreditAmount" class="w-full py-4 px-4 border-2 border-gray-200 rounded-xl font-semibold text-lg focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all duration-200" placeholder="Min {{ number_format($minCreditAmount) }} Credits" min="{{ $minCreditAmount }}">
                        @error('customCreditAmount')
                            <span class="text-red-500 text-sm font-medium mt-1 block">{{ $message }}</span>
                        @enderror
                        @if ($customCreditAmount && $customCreditAmount >= $minCreditAmount)
                            <div class="text-sm font-medium mt-2 bg-gray-50 p-3 rounded-lg text-gray-600">Cost: ₦{{ number_format($customCreditAmount * ($pricingConfig['credit_price'] ?? 1), 2) }}</div>
                        @endif
                    </div>

                    <!-- Purchase Button -->
                    <div class="flex space-x-4">
                        <button wire:click="closeCreditModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 px-4 rounded-xl transition-all duration-200">
                            Cancel
                        </button>
                        <button wire:click="purchaseCredits" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-4 px-4 rounded-xl disabled:opacity-50 transition-all duration-200">
                            <span wire:loading.remove wire:target="purchaseCredits">Buy Credits</span>
                            <span wire:loading wire:target="purchaseCredits" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
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
        <div x-show="$wire.showAirtimeModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50" @click.self="$wire.closeAirtimeModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-1 transform scale-100" class="bg-white rounded-3xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-t-3xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold">Buy Airtime</h3>
                        <button wire:click="closeAirtimeModal" class="text-white/80 hover:text-white transform hover:scale-110 transition-all duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Payment Method Selector -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Payment Method</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button wire:click="$set('airtimePaymentMethod', 'naira')" class="flex items-center justify-center p-4 border-2 rounded-xl font-semibold text-sm transition-all duration-200 {{ $airtimePaymentMethod === 'naira' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                Naira
                            </button>
                            <button wire:click="$set('airtimePaymentMethod', 'earnings')" class="flex items-center justify-center p-4 border-2 rounded-xl font-semibold text-sm transition-all duration-200 {{ $airtimePaymentMethod === 'earnings' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                Earnings
                            </button>
                        </div>
                    </div>

                    <!-- Phone Number Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 border-2 border-r-0 border-gray-200 bg-gray-50 text-gray-600 font-semibold rounded-l-xl">+234</span>
                            <input type="tel" wire:model.live="airtimePhoneNumber" class="flex-1 border-2 border-gray-200 font-semibold text-lg py-4 px-4 rounded-r-xl focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200" placeholder="8012345678" maxlength="10">
                        </div>
                        @error('airtimePhoneNumber')
                            <span class="text-red-500 text-sm font-medium mt-1 block">{{ $message }}</span>
                        @enderror
                        @if ($detectedAirtimeNetwork)
                            <div class="text-sm font-medium bg-blue-50 p-3 rounded-lg mt-2 text-blue-700">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Network: {{ $detectedAirtimeNetwork }}
                            </div>
                        @endif
                    </div>

                    <!-- Amount Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Amount</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">₦</span>
                            <input type="number" wire:model.live="airtimeAmount" class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl font-semibold text-lg focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200" placeholder="Enter amount" min="50" max="5000">
                        </div>
                        @error('airtimeAmount')
                            <span class="text-red-500 text-sm font-medium mt-1 block">{{ $message }}</span>
                        @enderror
                        <div class="text-xs font-medium mt-2 bg-gray-50 p-3 rounded-lg text-gray-600">Min: ₦50, Max: ₦5,000</div>
                    </div>

                    <!-- Purchase Button -->
                    <div class="flex space-x-4">
                        <button wire:click="closeAirtimeModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 px-4 rounded-xl transition-all duration-200">
                            Cancel
                        </button>
                        <button wire:click="purchaseAirtime" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold py-4 px-4 rounded-xl disabled:opacity-50 transition-all duration-200">
                            <span wire:loading.remove wire:target="purchaseAirtime">Buy Airtime</span>
                            <span wire:loading wire:target="purchaseAirtime" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 718-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Purchase Modal -->
        <div x-show="$wire.showDataModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50" @click.self="$wire.closeDataModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-1 transform scale-100" class="bg-white rounded-3xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-t-3xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold">Buy Data</h3>
                        <button wire:click="closeDataModal" class="text-white/80 hover:text-white transform hover:scale-110 transition-all duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Payment Method Selector -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Payment Method</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button wire:click="$set('dataPaymentMethod', 'naira')" class="flex items-center justify-center p-4 border-2 rounded-xl font-semibold text-sm transition-all duration-200 {{ $dataPaymentMethod === 'naira' ? 'border-orange-500 bg-orange-50 text-orange-700' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                Naira
                            </button>
                            <button wire:click="$set('dataPaymentMethod', 'earnings')" class="flex items-center justify-center p-4 border-2 rounded-xl font-semibold text-sm transition-all duration-200 {{ $dataPaymentMethod === 'earnings' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                Earnings
                            </button>
                        </div>
                    </div>

                    <!-- Phone Number Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 border-2 border-r-0 border-gray-200 bg-gray-50 text-gray-600 font-semibold rounded-l-xl">+234</span>
                            <input type="tel" wire:model.live="dataPhoneNumber" class="flex-1 border-2 border-gray-200 font-semibold text-lg py-4 px-4 rounded-r-xl focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all duration-200" placeholder="8012345678" maxlength="10">
                        </div>
                        @error('dataPhoneNumber')
                            <span class="text-red-500 text-sm font-medium mt-1 block">{{ $message }}</span>
                        @enderror
                        @if ($detectedDataNetwork)
                            <div class="text-sm font-medium bg-blue-50 p-3 rounded-lg mt-2 text-blue-700">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Network: {{ $detectedDataNetwork }}
                            </div>
                        @endif
                    </div>

                    <!-- Data Plan Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Select Data Plan</label>
                        <select wire:model.live="selectedDataPlan" class="w-full py-4 px-4 border-2 border-gray-200 rounded-xl font-semibold text-lg focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                            <option value="">Choose a plan</option>
                            @foreach ($availableDataPlans as $plan)
                                <option value="{{ $plan['plan_id'] }}">{{ $plan['size'] }} - {{ $plan['validity'] }} - ₦{{ number_format($plan['price'], 2) }}</option>
                            @endforeach
                        </select>
                        @error('selectedDataPlan')
                            <span class="text-red-500 text-sm font-medium mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Purchase Button -->
                    <div class="flex space-x-4">
                        <button wire:click="closeDataModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 px-4 rounded-xl transition-all duration-200">
                            Cancel
                        </button>
                        <button wire:click="purchaseData" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-4 px-4 rounded-xl disabled:opacity-50 transition-all duration-200">
                            <span wire:loading.remove wire:target="purchaseData">Buy Data</span>
                            <span wire:loading wire:target="purchaseData" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 718-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdrawal Modal -->
        <div x-show="$wire.showWithdrawModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50" @click.self="$wire.closeWithdrawModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-1 transform scale-100" class="bg-white rounded-3xl shadow-2xl w-full max-w-md max-h-[80vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-t-3xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold">Withdraw Earnings</h3>
                        <button wire:click="closeWithdrawModal" class="text-white/80 hover:text-white transform hover:scale-110 transition-all duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Withdrawal Methods -->
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center p-4 border-2 cursor-pointer font-semibold rounded-xl transition-all duration-200 {{ $withdrawalMethod === 'bank_account' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50' }}" wire:click="$set('withdrawalMethod', 'bank_account')">
                            <input type="radio" wire:model.live="withdrawalMethod" value="bank_account" class="mr-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span>Bank Transfer</span>
                            </div>
                        </div>

                        <div class="flex items-center p-4 border-2 cursor-pointer font-semibold rounded-xl transition-all duration-200 {{ $withdrawalMethod === 'palmpay' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50' }}" wire:click="$set('withdrawalMethod', 'palmpay')">
                            <input type="radio" wire:model.live="withdrawalMethod" value="palmpay" class="mr-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span>PalmPay</span>
                            </div>
                        </div>

                        @if ($airtimeApiEnabled)
                            <div class="flex items-center p-4 border-2 cursor-pointer font-semibold rounded-xl transition-all duration-200 {{ $withdrawalMethod === 'airtime' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50' }}" wire:click="$set('withdrawalMethod', 'airtime')">
                                <input type="radio" wire:model.live="withdrawalMethod" value="airtime" class="mr-4">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 717.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                                    </svg>
                                    <span>Airtime</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Method-specific Fields -->
                    @if ($withdrawalMethod === 'bank_account')
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bank</label>
                                <select wire:model.live="bankCode" class="w-full py-4 px-4 border-2 border-gray-200 rounded-xl font-semibold focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200">
                                    <option value="">Select bank</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('bankCode')
                                    <span class="text-red-500 text-sm font-medium mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Account Number</label>
                                <input type="text" wire:model.live="accountNumber" class="w-full py-4 px-4 border-2 border-gray-200 rounded-xl font-semibold focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200" placeholder="Enter account number" maxlength="10">
                                @error('accountNumber')
                                    <span class="text-red-500 text-sm font-medium mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($accountName)
                                <div class="text-sm font-medium bg-green-50 p-3 rounded-lg text-green-700">
                                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $accountName }}
                                </div>
                            @endif
                        </div>
                    @elseif($withdrawalMethod === 'palmpay')
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">PalmPay Number</label>
                            <input type="tel" wire:model.live="palmpayNumber" class="w-full py-4 px-4 border-2 border-gray-200 rounded-xl font-semibold focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200" placeholder="Enter PalmPay number" maxlength="11">
                            @error('palmpayNumber')
                                <span class="text-red-500 text-sm font-medium mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @elseif($withdrawalMethod === 'airtime')
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" wire:model.live="airtimeNumber" class="w-full py-4 px-4 border-2 border-gray-200 rounded-xl font-semibold focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200" placeholder="Enter phone number" maxlength="11">
                                @error('airtimeNumber')
                                    <span class="text-red-500 text-sm font-medium mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($detectedNetwork)
                                <div class="text-sm font-medium bg-blue-50 p-3 rounded-lg text-blue-700">Detected: {{ $detectedNetwork }}</div>
                            @endif
                            @if ($networks && count($networks) > 0)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Network</label>
                                    <select wire:model.live="airtimeNetwork" class="w-full py-4 px-4 border-2 border-gray-200 rounded-xl font-semibold focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200">
                                        <option value="">Select network</option>
                                        @foreach ($networks as $networkId => $networkName)
                                            <option value="{{ $networkId }}">{{ $networkName }}</option>
                                        @endforeach
                                    </select>
                                    @error('airtimeNetwork')
                                        <span class="text-red-500 text-sm font-medium mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Amount Field -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (₦)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">₦</span>
                            <input type="number" wire:model.live="amount" class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl font-semibold text-lg focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200" placeholder="Enter amount" min="1000">
                        </div>
                        @error('amount')
                            <span class="text-red-500 text-sm font-medium mt-1 block">{{ $message }}</span>
                        @enderror
                        @if (isset($fees['total']) && $fees['total'] > 0)
                            <div class="bg-gray-50 p-3 rounded-lg mt-2 space-y-1">
                                <div class="text-sm font-medium text-gray-600">Fee: ₦{{ number_format($fees['total'], 2) }}</div>
                                <div class="text-sm font-semibold text-gray-800">You'll receive: ₦{{ number_format($netAmount, 2) }}</div>
                            </div>
                        @endif
                        <div class="text-xs font-medium mt-2 text-gray-500">Minimum: ₦1,000</div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex space-x-4">
                        <button wire:click="closeWithdrawModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 px-4 rounded-xl transition-all duration-200">
                            Cancel
                        </button>
                        <button wire:click="processWithdrawal" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold py-4 px-4 rounded-xl disabled:opacity-50 transition-all duration-200">
                            <span wire:loading.remove wire:target="processWithdrawal">Withdraw</span>
                            <span wire:loading wire:target="processWithdrawal" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 718-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
            /* Modern Elegant Design System */
            * {
                font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            }
            
            /* Smooth transitions for all interactive elements */
            .transition-all {
                transition-property: all;
                transition-timing-function: cubic-bezier(0.4, 0.0, 0.2, 1);
            }
            
            /* Custom gradient animations */
            @keyframes gradient-shift {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }
            
            .bg-gradient-to-r {
                background-size: 200% 200%;
                animation: gradient-shift 6s ease infinite;
            }
            
            /* Enhanced focus states */
            input:focus, select:focus {
                outline: none;
                box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.1);
                border-color: #a855f7;
            }
            
            /* Smooth hover effects for cards */
            .hover\:shadow-xl:hover {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            }
            
            /* Backdrop blur support */
            .backdrop-blur-sm {
                backdrop-filter: blur(4px);
            }
            
            .backdrop-blur {
                backdrop-filter: blur(8px);
            }
            
            .backdrop-blur-lg {
                backdrop-filter: blur(16px);
            }
            
            .backdrop-blur-xl {
                backdrop-filter: blur(24px);
            }
            
            /* Loading spinner improvements */
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            
            /* Mobile responsiveness */
            @media (max-width: 768px) {
                .rounded-3xl { border-radius: 1.5rem; }
                .p-8 { padding: 1.5rem; }
                .p-6 { padding: 1rem; }
                .text-6xl { font-size: 2.5rem; }
                .gap-6 { gap: 1rem; }
            }
            
            /* Enhanced button states */
            button:active {
                transform: scale(0.98);
            }
            
            button:disabled {
                cursor: not-allowed;
                opacity: 0.6;
            }
            
            /* Custom scrollbar for modals */
            .overflow-y-auto::-webkit-scrollbar {
                width: 6px;
            }
            
            .overflow-y-auto::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 3px;
            }
            
            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 3px;
            }
            
            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
            
            /* Elegant gradient text */
            .gradient-text {
                background: linear-gradient(135deg, #a855f7, #f97316);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://js.paystack.co/v1/inline.js"></script>
        <script>
            function walletApp() {
                return {
                    showFlash: true,
                    
                    init() {
                        // Auto-hide flash messages after 6 seconds with fade out
                        if (this.showFlash) {
                            setTimeout(() => {
                                this.showFlash = false;
                            }, 6000);
                        }
                        
                        // Add haptic feedback for supported devices
                        if ('vibrate' in navigator) {
                            document.addEventListener('click', function(e) {
                                if (e.target.closest('button')) {
                                    navigator.vibrate(10);
                                }
                            });
                        }
                        
                        // Handle escape key to close modals
                        document.addEventListener('keydown', (e) => {
                            if (e.key === 'Escape') {
                                Livewire.dispatch('closeAllModals');
                            }
                        });
                        
                        // Smooth scroll behavior
                        document.documentElement.style.scrollBehavior = 'smooth';
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