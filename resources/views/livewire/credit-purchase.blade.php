<div class="" x-data="walletApp()" x-init="init()">
    <div class="min-h-screen bg-white">
        <!-- Header Section -->
        <div class="bg-black text-white">
            <div class="max-w-4xl mx-auto px-6 py-8">
                <!-- Flash Messages -->
                @if (session()->has('success') || session()->has('error') || session()->has('info'))
                    <div class="mb-6" x-show="showFlash" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-1 transform translate-y-0">
                        @if (session()->has('success'))
                            <div class="bg-green-500 text-white p-4 border-l-8 border-green-600 shadow-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="font-bold">{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="bg-red-500 text-white p-4 border-l-8 border-red-600 shadow-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="font-bold">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif
                        @if (session()->has('info'))
                            <div class="bg-blue-500 text-white p-4 border-l-8 border-blue-600 shadow-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="font-bold">{{ session('info') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Wallet Balance Card -->
                <div class="bg-white text-black border-8 border-black shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] p-8 mb-8 transform hover:translate-x-1 hover:translate-y-1 hover:shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] transition-all duration-150">
                    <!-- Main Balance -->
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center">
                            <div class="w-16 h-16 bg-black text-white flex items-center justify-center mr-6">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-sm font-black uppercase tracking-wider mb-1">NAIRA WALLET</h1>
                                <p class="text-xs uppercase tracking-wide">TOTAL AVAILABLE</p>
                            </div>
                        </div>
                        <button wire:click="openFundModal" class="bg-black text-white px-6 py-3 font-black uppercase tracking-wide border-4 border-black hover:bg-white hover:text-black transform hover:scale-105 active:scale-95 transition-all duration-150" wire:loading.attr="disabled">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span wire:loading.remove wire:target="openFundModal">ADD MONEY</span>
                                <span wire:loading wire:target="openFundModal">LOADING...</span>
                            </span>
                        </button>
                    </div>
                    
                    <div class="text-6xl font-black mb-8 tracking-tight">₦{{ number_format($user->getNairaWallet()->balance, 2) }}</div>
                    
                    <!-- Other Wallets Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Credits Wallet -->
                        <div class="bg-yellow-400 text-black border-4 border-black p-6 transform hover:scale-105 transition-all duration-150">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-black text-yellow-400 flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <span class="font-black uppercase tracking-wide">CREDITS</span>
                            </div>
                            <div class="text-3xl font-black mb-3">{{ number_format($user->getCreditWallet()->balance) }}</div>
                            <button wire:click="openCreditModal" class="bg-black text-yellow-400 px-4 py-2 font-black uppercase text-xs tracking-wide hover:bg-yellow-400 hover:text-black transform hover:scale-105 active:scale-95 transition-all duration-150" wire:loading.attr="disabled">
                                BUY CREDITS →
                            </button>
                        </div>
                        
                        <!-- Earnings Wallet -->
                        <div class="bg-green-400 text-black border-4 border-black p-6 transform hover:scale-105 transition-all duration-150">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-black text-green-400 flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="font-black uppercase tracking-wide">EARNINGS</span>
                            </div>
                            <div class="text-3xl font-black mb-3">₦{{ number_format($user->getEarningsWallet()->balance, 2) }}</div>
                            <button wire:click="openWithdrawModal" class="bg-black text-green-400 px-4 py-2 font-black uppercase text-xs tracking-wide hover:bg-green-400 hover:text-black transform hover:scale-105 active:scale-95 transition-all duration-150" wire:loading.attr="disabled">
                                WITHDRAW →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-6 py-8">
            <!-- Quick Actions Grid -->
            <div class="bg-white border-8 border-black shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] p-8 mb-8">
                <h2 class="text-3xl font-black uppercase tracking-wide mb-8">QUICK ACTIONS</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Airtime -->
                    <button wire:click="openAirtimeModal" class="bg-purple-400 text-black border-4 border-black p-6 font-black uppercase tracking-wide transform hover:scale-105 hover:shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] active:scale-95 transition-all duration-150" wire:loading.attr="disabled">
                        <div class="w-16 h-16 bg-black text-purple-400 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8.111 16.404a5.5 5.5 0 717.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                            </svg>
                        </div>
                        <span>AIRTIME</span>
                    </button>

                    <!-- Data -->
                    <button wire:click="openDataModal" class="bg-blue-400 text-black border-4 border-black p-6 font-black uppercase tracking-wide transform hover:scale-105 hover:shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] active:scale-95 transition-all duration-150" wire:loading.attr="disabled">
                        <div class="w-16 h-16 bg-black text-blue-400 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span>DATA</span>
                    </button>

                    <!-- Bills -->
                    <button class="bg-red-400 text-black border-4 border-black p-6 font-black uppercase tracking-wide transform hover:scale-105 hover:shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] active:scale-95 transition-all duration-150">
                        <div class="w-16 h-16 bg-black text-red-400 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span>BILLS</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Fund Wallet Modal -->
        <div x-show="$wire.showFundModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50" @click.self="$wire.closeFundModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-1 transform scale-100" class="bg-white border-8 border-black shadow-[16px_16px_0px_0px_rgba(0,0,0,1)] w-full max-w-md max-h-[80vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="bg-black text-white p-6 border-b-4 border-black">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black uppercase tracking-wide">FUND WALLET</h3>
                        <button wire:click="closeFundModal" class="text-white hover:text-gray-300 transform hover:scale-110 active:scale-95 transition-all duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Amount Field -->
                    <div class="mb-6">
                        <label class="block text-sm font-black uppercase tracking-wide mb-3">AMOUNT (₦)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-black font-black">₦</span>
                            <input type="number" wire:model.live="fundAmount" class="w-full pl-12 pr-4 py-4 border-4 border-black font-black text-lg focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150" placeholder="ENTER AMOUNT" min="{{ $pricingConfig['minimum_amount'] ?? 100 }}">
                        </div>
                        @error('fundAmount')
                            <span class="text-red-500 text-sm font-bold mt-2 block uppercase">{{ $message }}</span>
                        @enderror
                        <div class="text-xs font-bold mt-2 bg-gray-100 p-3 border-2 border-black uppercase tracking-wide">MINIMUM: ₦{{ number_format($pricingConfig['minimum_amount'] ?? 100) }}</div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-blue-100 border-4 border-black p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-black mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm font-bold">
                                <p class="font-black mb-2 uppercase tracking-wide">PAYMENT VIA PAYSTACK</p>
                                <p class="text-xs uppercase">SECURE PAYMENT PROCESSING</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex space-x-4">
                        <button wire:click="closeFundModal" class="flex-1 bg-gray-200 text-black font-black py-4 px-4 border-4 border-black uppercase tracking-wide hover:bg-gray-300 transform hover:scale-105 active:scale-95 transition-all duration-150">
                            CANCEL
                        </button>
                        <button wire:click="fundWallet" wire:loading.attr="disabled" class="flex-1 bg-black text-white font-black py-4 px-4 border-4 border-black uppercase tracking-wide hover:bg-gray-800 transform hover:scale-105 active:scale-95 disabled:opacity-50 transition-all duration-150">
                            <span wire:loading.remove wire:target="fundWallet">PROCEED</span>
                            <span wire:loading wire:target="fundWallet" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                PROCESSING...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Credit Purchase Modal -->
        <div x-show="$wire.showCreditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50" @click.self="$wire.closeCreditModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-1 transform scale-100" class="bg-white border-8 border-black shadow-[16px_16px_0px_0px_rgba(0,0,0,1)] w-full max-w-md max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="bg-black text-white p-6 border-b-4 border-black">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black uppercase tracking-wide">BUY CREDITS</h3>
                        <button wire:click="closeCreditModal" class="text-white hover:text-gray-300 transform hover:scale-110 active:scale-95 transition-all duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Payment Method Selector -->
                    <div class="mb-6">
                        <label class="block text-sm font-black uppercase tracking-wide mb-3">PAYMENT METHOD</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button wire:click="$set('creditPaymentMethod', 'naira')" class="flex items-center justify-center p-4 border-4 border-black font-black uppercase text-sm transition-all duration-150 {{ $creditPaymentMethod === 'naira' ? 'bg-yellow-400 text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]' : 'bg-gray-100 text-black hover:bg-gray-200' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                NAIRA
                            </button>
                            <button wire:click="$set('creditPaymentMethod', 'earnings')" class="flex items-center justify-center p-4 border-4 border-black font-black uppercase text-sm transition-all duration-150 {{ $creditPaymentMethod === 'earnings' ? 'bg-green-400 text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]' : 'bg-gray-100 text-black hover:bg-gray-200' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                EARNINGS
                            </button>
                        </div>
                    </div>

                    <!-- Credit Package Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-black uppercase tracking-wide mb-3">CHOOSE PACKAGE</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($creditPackages as $index => $package)
                                <div class="relative">
                                    <input type="radio" wire:click="selectCreditPackage({{ $index }})" name="credit_package" id="credit-package-{{ $index }}" class="sr-only peer">
                                    <label for="credit-package-{{ $index }}" class="block p-4 border-4 border-black cursor-pointer font-black uppercase text-center transition-all duration-150 transform hover:scale-105 active:scale-95 {{ $selectedCreditPackage === $index ? 'bg-yellow-400 text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]' : 'bg-gray-100 text-black hover:bg-gray-200' }}">
                                        @if ($package['bonus'] > 0)
                                            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-black px-2 py-1 border-2 border-black">
                                                +{{ number_format($package['bonus']) }}
                                            </div>
                                        @endif
                                        <div class="text-lg font-black mb-1">{{ number_format($package['credits']) }}</div>
                                        <div class="text-xs mb-2">CREDITS</div>
                                        @if ($package['bonus'] > 0)
                                            <div class="text-xs text-green-600 font-black mb-2">TOTAL: {{ number_format($package['total_credits']) }}</div>
                                        @endif
                                        <div class="text-base font-black">₦{{ number_format($package['amount']) }}</div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Custom Amount Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-black uppercase tracking-wide mb-2">CUSTOM AMOUNT (CREDITS)</label>
                        <input type="number" wire:model.live="customCreditAmount" class="w-full py-4 px-4 border-4 border-black font-bold text-lg focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150" placeholder="MIN {{ number_format($minCreditAmount) }} CREDITS" min="{{ $minCreditAmount }}">
                        @error('customCreditAmount')
                            <span class="text-red-500 text-sm font-bold mt-1 block uppercase">{{ $message }}</span>
                        @enderror
                        @if ($customCreditAmount && $customCreditAmount >= $minCreditAmount)
                            <div class="text-sm font-bold mt-2 bg-gray-100 p-3 border-2 border-black uppercase">COST: ₦{{ number_format($customCreditAmount * ($pricingConfig['credit_price'] ?? 1), 2) }}</div>
                        @endif
                    </div>

                    <!-- Purchase Button -->
                    <div class="flex space-x-4">
                        <button wire:click="closeCreditModal" class="flex-1 bg-gray-200 text-black font-black py-4 px-4 border-4 border-black uppercase tracking-wide hover:bg-gray-300 transform hover:scale-105 active:scale-95 transition-all duration-150">
                            CANCEL
                        </button>
                        <button wire:click="purchaseCredits" wire:loading.attr="disabled" class="flex-1 bg-yellow-400 text-black font-black py-4 px-4 border-4 border-black uppercase tracking-wide hover:bg-yellow-500 transform hover:scale-105 active:scale-95 disabled:opacity-50 transition-all duration-150">
                            <span wire:loading.remove wire:target="purchaseCredits">BUY CREDITS</span>
                            <span wire:loading wire:target="purchaseCredits" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                PROCESSING...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Airtime Purchase Modal -->
        <div x-show="$wire.showAirtimeModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50" @click.self="$wire.closeAirtimeModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-1 transform scale-100" class="bg-white border-8 border-black shadow-[16px_16px_0px_0px_rgba(0,0,0,1)] w-full max-w-md max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="bg-black text-white p-6 border-b-4 border-black">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black uppercase tracking-wide">BUY AIRTIME</h3>
                        <button wire:click="closeAirtimeModal" class="text-white hover:text-gray-300 transform hover:scale-110 active:scale-95 transition-all duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Payment Method Selector -->
                    <div class="mb-6">
                        <label class="block text-sm font-black uppercase tracking-wide mb-3">PAYMENT METHOD</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button wire:click="$set('airtimePaymentMethod', 'naira')" class="flex items-center justify-center p-4 border-4 border-black font-black uppercase text-sm transition-all duration-150 {{ $airtimePaymentMethod === 'naira' ? 'bg-purple-400 text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]' : 'bg-gray-100 text-black hover:bg-gray-200' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                NAIRA
                            </button>
                            <button wire:click="$set('airtimePaymentMethod', 'earnings')" class="flex items-center justify-center p-4 border-4 border-black font-black uppercase text-sm transition-all duration-150 {{ $airtimePaymentMethod === 'earnings' ? 'bg-green-400 text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]' : 'bg-gray-100 text-black hover:bg-gray-200' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                EARNINGS
                            </button>
                        </div>
                    </div>

                    <!-- Phone Number Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-black uppercase tracking-wide mb-2">PHONE NUMBER</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 border-4 border-r-0 border-black bg-gray-100 text-black font-black">+234</span>
                            <input type="tel" wire:model.live="airtimePhoneNumber" class="flex-1 border-4 border-black font-bold text-lg py-4 px-4 focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150" placeholder="8012345678" maxlength="10">
                        </div>
                        @error('airtimePhoneNumber')
                            <span class="text-red-500 text-sm font-bold mt-1 block uppercase">{{ $message }}</span>
                        @enderror
                        @if ($detectedAirtimeNetwork)
                            <div class="text-sm font-bold bg-blue-100 p-3 border-2 border-black mt-2">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                NETWORK: {{ $detectedAirtimeNetwork }}
                            </div>
                        @endif
                    </div>

                    <!-- Amount Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-black uppercase tracking-wide mb-2">AMOUNT</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-black font-black">₦</span>
                            <input type="number" wire:model.live="airtimeAmount" class="w-full pl-12 pr-4 py-4 border-4 border-black font-bold text-lg focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150" placeholder="ENTER AMOUNT" min="50" max="5000">
                        </div>
                        @error('airtimeAmount')
                            <span class="text-red-500 text-sm font-bold mt-1 block uppercase">{{ $message }}</span>
                        @enderror
                        <div class="text-xs font-bold mt-2 bg-gray-100 p-3 border-2 border-black uppercase">MIN: ₦50, MAX: ₦5,000</div>
                    </div>

                    <!-- Purchase Button -->
                    <div class="flex space-x-4">
                        <button wire:click="closeAirtimeModal" class="flex-1 bg-gray-200 text-black font-black py-4 px-4 border-4 border-black uppercase tracking-wide hover:bg-gray-300 transform hover:scale-105 active:scale-95 transition-all duration-150">
                            CANCEL
                        </button>
                        <button wire:click="purchaseAirtime" wire:loading.attr="disabled" class="flex-1 bg-purple-400 text-black font-black py-4 px-4 border-4 border-black uppercase tracking-wide hover:bg-purple-500 transform hover:scale-105 active:scale-95 disabled:opacity-50 transition-all duration-150">
                            <span wire:loading.remove wire:target="purchaseAirtime">BUY AIRTIME</span>
                            <span wire:loading wire:target="purchaseAirtime" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 718-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                PROCESSING...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Purchase Modal -->
        <div x-show="$wire.showDataModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50" @click.self="$wire.closeDataModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-1 transform scale-100" class="bg-white border-8 border-black shadow-[16px_16px_0px_0px_rgba(0,0,0,1)] w-full max-w-md max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="bg-black text-white p-6 border-b-4 border-black">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black uppercase tracking-wide">BUY DATA</h3>
                        <button wire:click="closeDataModal" class="text-white hover:text-gray-300 transform hover:scale-110 active:scale-95 transition-all duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Payment Method Selector -->
                    <div class="mb-6">
                        <label class="block text-sm font-black uppercase tracking-wide mb-3">PAYMENT METHOD</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button wire:click="$set('dataPaymentMethod', 'naira')" class="flex items-center justify-center p-4 border-4 border-black font-black uppercase text-sm transition-all duration-150 {{ $dataPaymentMethod === 'naira' ? 'bg-blue-400 text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]' : 'bg-gray-100 text-black hover:bg-gray-200' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                NAIRA
                            </button>
                            <button wire:click="$set('dataPaymentMethod', 'earnings')" class="flex items-center justify-center p-4 border-4 border-black font-black uppercase text-sm transition-all duration-150 {{ $dataPaymentMethod === 'earnings' ? 'bg-green-400 text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]' : 'bg-gray-100 text-black hover:bg-gray-200' }}">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                EARNINGS
                            </button>
                        </div>
                    </div>

                    <!-- Phone Number Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-black uppercase tracking-wide mb-2">PHONE NUMBER</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 border-4 border-r-0 border-black bg-gray-100 text-black font-black">+234</span>
                            <input type="tel" wire:model.live="dataPhoneNumber" class="flex-1 border-4 border-black font-bold text-lg py-4 px-4 focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150" placeholder="8012345678" maxlength="10">
                        </div>
                        @error('dataPhoneNumber')
                            <span class="text-red-500 text-sm font-bold mt-1 block uppercase">{{ $message }}</span>
                        @enderror
                        @if ($detectedDataNetwork)
                            <div class="text-sm font-bold bg-blue-100 p-3 border-2 border-black mt-2">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                NETWORK: {{ $detectedDataNetwork }}
                            </div>
                        @endif
                    </div>

                    <!-- Data Plan Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-black uppercase tracking-wide mb-2">SELECT DATA PLAN</label>
                        <select wire:model.live="selectedDataPlan" class="w-full py-4 px-4 border-4 border-black font-bold text-lg focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150">
                            <option value="">CHOOSE A PLAN</option>
                            @foreach ($availableDataPlans as $plan)
                                <option value="{{ $plan['plan_id'] }}">{{ $plan['size'] }} - {{ $plan['validity'] }} - ₦{{ number_format($plan['price'], 2) }}</option>
                            @endforeach
                        </select>
                        @error('selectedDataPlan')
                            <span class="text-red-500 text-sm font-bold mt-1 block uppercase">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Purchase Button -->
                    <div class="flex space-x-4">
                        <button wire:click="closeDataModal" class="flex-1 bg-gray-200 text-black font-black py-4 px-4 border-4 border-black uppercase tracking-wide hover:bg-gray-300 transform hover:scale-105 active:scale-95 transition-all duration-150">
                            CANCEL
                        </button>
                        <button wire:click="purchaseData" wire:loading.attr="disabled" class="flex-1 bg-blue-400 text-black font-black py-4 px-4 border-4 border-black uppercase tracking-wide hover:bg-blue-500 transform hover:scale-105 active:scale-95 disabled:opacity-50 transition-all duration-150">
                            <span wire:loading.remove wire:target="purchaseData">BUY DATA</span>
                            <span wire:loading wire:target="purchaseData" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 718-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                PROCESSING...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdrawal Modal -->
        <div x-show="$wire.showWithdrawModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50" @click.self="$wire.closeWithdrawModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-1 transform scale-100" class="bg-white border-8 border-black shadow-[16px_16px_0px_0px_rgba(0,0,0,1)] w-full max-w-md max-h-[80vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="bg-black text-white p-6 border-b-4 border-black">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black uppercase tracking-wide">WITHDRAW EARNINGS</h3>
                        <button wire:click="closeWithdrawModal" class="text-white hover:text-gray-300 transform hover:scale-110 active:scale-95 transition-all duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Withdrawal Methods -->
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center p-4 border-4 border-black cursor-pointer font-black uppercase transition-all duration-150 transform hover:scale-105 {{ $withdrawalMethod === 'bank_account' ? 'bg-blue-400 text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]' : 'bg-gray-100 text-black hover:bg-gray-200' }}" wire:click="$set('withdrawalMethod', 'bank_account')">
                            <input type="radio" wire:model.live="withdrawalMethod" value="bank_account" class="mr-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span>BANK TRANSFER</span>
                            </div>
                        </div>

                        <div class="flex items-center p-4 border-4 border-black cursor-pointer font-black uppercase transition-all duration-150 transform hover:scale-105 {{ $withdrawalMethod === 'palmpay' ? 'bg-green-400 text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]' : 'bg-gray-100 text-black hover:bg-gray-200' }}" wire:click="$set('withdrawalMethod', 'palmpay')">
                            <input type="radio" wire:model.live="withdrawalMethod" value="palmpay" class="mr-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span>PALMPAY</span>
                            </div>
                        </div>

                        @if ($airtimeApiEnabled)
                            <div class="flex items-center p-4 border-4 border-black cursor-pointer font-black uppercase transition-all duration-150 transform hover:scale-105 {{ $withdrawalMethod === 'airtime' ? 'bg-purple-400 text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]' : 'bg-gray-100 text-black hover:bg-gray-200' }}" wire:click="$set('withdrawalMethod', 'airtime')">
                                <input type="radio" wire:model.live="withdrawalMethod" value="airtime" class="mr-4">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8.111 16.404a5.5 5.5 0 717.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                                    </svg>
                                    <span>AIRTIME</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Method-specific Fields -->
                    @if ($withdrawalMethod === 'bank_account')
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-black uppercase tracking-wide mb-2">BANK</label>
                                <select wire:model.live="bankCode" class="w-full py-4 px-4 border-4 border-black font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150">
                                    <option value="">SELECT BANK</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('bankCode')
                                    <span class="text-red-500 text-sm font-bold mt-1 block uppercase">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-black uppercase tracking-wide mb-2">ACCOUNT NUMBER</label>
                                <input type="text" wire:model.live="accountNumber" class="w-full py-4 px-4 border-4 border-black font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150" placeholder="ENTER ACCOUNT NUMBER" maxlength="10">
                                @error('accountNumber')
                                    <span class="text-red-500 text-sm font-bold mt-1 block uppercase">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($accountName)
                                <div class="text-sm font-bold bg-green-100 p-3 border-2 border-black">
                                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $accountName }}
                                </div>
                            @endif
                        </div>
                    @elseif($withdrawalMethod === 'palmpay')
                        <div class="mb-6">
                            <label class="block text-sm font-black uppercase tracking-wide mb-2">PALMPAY NUMBER</label>
                            <input type="tel" wire:model.live="palmpayNumber" class="w-full py-4 px-4 border-4 border-black font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150" placeholder="ENTER PALMPAY NUMBER" maxlength="11">
                            @error('palmpayNumber')
                                <span class="text-red-500 text-sm font-bold mt-1 block uppercase">{{ $message }}</span>
                            @enderror
                        </div>
                    @elseif($withdrawalMethod === 'airtime')
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-black uppercase tracking-wide mb-2">PHONE NUMBER</label>
                                <input type="tel" wire:model.live="airtimeNumber" class="w-full py-4 px-4 border-4 border-black font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150" placeholder="ENTER PHONE NUMBER" maxlength="11">
                                @error('airtimeNumber')
                                    <span class="text-red-500 text-sm font-bold mt-1 block uppercase">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($detectedNetwork)
                                <div class="text-sm font-bold bg-blue-100 p-3 border-2 border-black uppercase">DETECTED: {{ $detectedNetwork }}</div>
                            @endif
                            @if ($networks && count($networks) > 0)
                                <div>
                                    <label class="block text-sm font-black uppercase tracking-wide mb-2">NETWORK</label>
                                    <select wire:model.live="airtimeNetwork" class="w-full py-4 px-4 border-4 border-black font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150">
                                        <option value="">SELECT NETWORK</option>
                                        @foreach ($networks as $networkId => $networkName)
                                            <option value="{{ $networkId }}">{{ $networkName }}</option>
                                        @endforeach
                                    </select>
                                    @error('airtimeNetwork')
                                        <span class="text-red-500 text-sm font-bold mt-1 block uppercase">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Amount Field -->
                    <div class="mb-6">
                        <label class="block text-sm font-black uppercase tracking-wide mb-2">AMOUNT (₦)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-black font-black">₦</span>
                            <input type="number" wire:model.live="amount" class="w-full pl-12 pr-4 py-4 border-4 border-black font-bold text-lg focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-150" placeholder="ENTER AMOUNT" min="1000">
                        </div>
                        @error('amount')
                            <span class="text-red-500 text-sm font-bold mt-1 block uppercase">{{ $message }}</span>
                        @enderror
                        @if (isset($fees['total']) && $fees['total'] > 0)
                            <div class="bg-gray-100 p-3 border-2 border-black mt-2 space-y-1">
                                <div class="text-sm font-bold uppercase">FEE: ₦{{ number_format($fees['total'], 2) }}</div>
                                <div class="text-sm font-black uppercase">YOU'LL RECEIVE: ₦{{ number_format($netAmount, 2) }}</div>
                            </div>
                        @endif
                        <div class="text-xs font-bold mt-2 uppercase">MINIMUM: ₦1,000</div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex space-x-4">
                        <button wire:click="closeWithdrawModal" class="flex-1 bg-gray-200 text-black font-black py-4 px-4 border-4 border-black uppercase tracking-wide hover:bg-gray-300 transform hover:scale-105 active:scale-95 transition-all duration-150">
                            CANCEL
                        </button>
                        <button wire:click="processWithdrawal" wire:loading.attr="disabled" class="flex-1 bg-green-400 text-black font-black py-4 px-4 border-4 border-black uppercase tracking-wide hover:bg-green-500 transform hover:scale-105 active:scale-95 disabled:opacity-50 transition-all duration-150">
                            <span wire:loading.remove wire:target="processWithdrawal">WITHDRAW</span>
                            <span wire:loading wire:target="processWithdrawal" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 718-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                PROCESSING...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Brutalist Design System */
            * {
                font-family: 'Courier New', monospace;
            }
            
            /* Enhanced micro-interactions */
            .transform {
                transition: all 0.15s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            }
            
            .hover\:scale-105:hover {
                transform: scale(1.05);
            }
            
            .hover\:scale-110:hover {
                transform: scale(1.1);
            }
            
            .active\:scale-95:active {
                transform: scale(0.95);
            }
            
            /* Brutalist shadows */
            .shadow-brutal {
                box-shadow: 8px 8px 0px 0px rgba(0,0,0,1);
            }
            
            .shadow-brutal-lg {
                box-shadow: 12px 12px 0px 0px rgba(0,0,0,1);
            }
            
            .shadow-brutal-xl {
                box-shadow: 16px 16px 0px 0px rgba(0,0,0,1);
            }
            
            /* Focus states */
            input:focus, select:focus {
                outline: none;
                box-shadow: 4px 4px 0px 0px rgba(0,0,0,1);
            }
            
            /* Loading animation enhancement */
            @keyframes bounce {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }
            
            .animate-bounce {
                animation: bounce 1s infinite;
            }
            
            /* Mobile responsiveness */
            @media (max-width: 768px) {
                .border-8 { border-width: 4px; }
                .shadow-\[16px_16px_0px_0px_rgba\(0\,0\,0\,1\)\] {
                    box-shadow: 8px 8px 0px 0px rgba(0,0,0,1);
                }
                .shadow-\[12px_12px_0px_0px_rgba\(0\,0\,0\,1\)\] {
                    box-shadow: 6px 6px 0px 0px rgba(0,0,0,1);
                }
                .text-6xl { font-size: 3rem; }
                .p-8 { padding: 1rem; }
                .p-6 { padding: 1rem; }
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
                        // Auto-hide flash messages after 6 seconds
                        if (this.showFlash) {
                            setTimeout(() => {
                                this.showFlash = false;
                            }, 6000);
                        }
                        
                        // Add haptic feedback for supported devices
                        if ('vibrate' in navigator) {
                            document.addEventListener('click', function(e) {
                                if (e.target.closest('button')) {
                                    navigator.vibrate(25);
                                }
                            });
                        }
                        
                        // Handle escape key to close modals via Livewire
                        document.addEventListener('keydown', (e) => {
                            if (e.key === 'Escape') {
                                Livewire.dispatch('closeAllModals');
                            }
                        });
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