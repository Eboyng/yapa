<div class="" x-data="walletApp()" x-init="init()">
    <div class="min-h-screen bg-gray-50">
        <!-- Header Section -->
        <div class="0 text-white">
            <div class="max-w-lg mx-auto px-4 pt-4 pb-6">
                <!-- Flash Messages -->
                @if (session()->has('success') || session()->has('error') || session()->has('info'))
                    <div class="mb-4" x-show="showFlash" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-1 transform translate-y-0">
                        @if (session()->has('success'))
                            <div class="bg-green-100 border border-green-300 rounded-lg p-3 text-green-800">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm">{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="bg-red-100 border border-red-300 rounded-lg p-3 text-red-800">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif
                        @if (session()->has('info'))
                            <div class="bg-blue-100 border border-blue-300 rounded-lg p-3 text-blue-800">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm">{{ session('info') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Available Balance Card -->
                <div class="bg-emerald-500 backdrop-blur-md rounded-2xl p-6 mb-3">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                            </div>
                            <span class="text-white/90 text-sm font-medium">Available Balance</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-white/70 text-xs">Naira Wallet</span>
                            <svg class="w-4 h-4 ml-1 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                   <div class="flex justify-between items-center">
                     <div class="text-3xl lg:text-4xl font-bold mb-2">₦{{ number_format($user->getNairaWallet()->balance, 2) }}</div>
                    <div class="">
                         <button wire:click="openFundModal" class="bg-white text-green-600 px-6 py-2 rounded-full text-sm font-semibold hover:bg-white/90 transition-all transform hover:scale-105 active:scale-95" wire:loading.attr="disabled">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span wire:loading.remove wire:target="openFundModal">Add Money</span>
                            <span wire:loading wire:target="openFundModal">Loading...</span>
                        </span>
                    </button>
                    </div>
                   </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-lg mx-auto px-4 -mt-2">
            <!-- Other Wallets Summary -->
            <div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Credits Wallet -->
                    <div class="text-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <div class="text-lg font-bold text-gray-900">{{ number_format($user->getCreditWallet()->balance) }}</div>
                        <div class="text-xs text-gray-500">Credits</div>
                    </div>
                    
                    <!-- Earnings Wallet -->
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="text-lg font-bold text-gray-900">₦{{ number_format($user->getEarningsWallet()->balance, 2) }}</div>
                        <div class="text-xs text-gray-500">Earnings</div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Grid -->
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                <div class="grid grid-cols-3 gap-4">
                    <!-- Fund Wallet -->
                    <button wire:click="openFundModal" class="flex flex-col items-center p-4 rounded-xl hover:bg-gray-50 transition-colors group" wire:loading.attr="disabled">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-700">Add Money</span>
                    </button>

                    <!-- Withdraw -->
                    <button wire:click="openWithdrawModal" class="flex flex-col items-center p-4 rounded-xl hover:bg-gray-50 transition-colors group" wire:loading.attr="disabled">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-700">Withdraw</span>
                    </button>

                    <!-- Credits -->
                    <button @click="showCreditSection = !showCreditSection" class="flex flex-col items-center p-4 rounded-xl hover:bg-gray-50 transition-colors group">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-700">Credits</span>
                    </button>
                </div>
            </div>

            <!-- Credit Purchase Section -->
            <div x-show="showCreditSection" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-1 transform translate-y-0" class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Purchase Credits</h3>
                    <button @click="showCreditSection = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Info Banner -->
                <div class="bg-gradient-to-r from-orange-50 to-purple-50 border border-orange-200 rounded-lg p-3 mb-4">
                    <div class="text-sm text-gray-700 space-y-1">
                        <div class="flex items-center">
                            <span class="mr-2">💳</span>
                            <span>Use your Naira wallet balance</span>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-2">⚡</span>
                            <span>Instant credit purchase</span>
                        </div>
                        <div class="flex items-center text-orange-600 font-medium">
                            <span class="mr-2">🎁</span>
                            <span>Bonus credits on large packages!</span>
                        </div>
                    </div>
                </div>

                <!-- Credit Package Selection -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    @foreach ($creditPackages as $index => $package)
                        <div class="relative">
                            <input type="radio" wire:click="selectCreditPackage({{ $index }})" name="credit_package" id="credit-package-{{ $index }}" class="sr-only peer">
                            <label for="credit-package-{{ $index }}" class="block p-3 border-2 rounded-lg cursor-pointer transition-all duration-200 hover:scale-105 active:scale-95 {{ $selectedCreditPackage === $index ? 'border-orange-500 bg-gradient-to-br from-orange-50 to-purple-50 shadow-md' : 'border-gray-200 hover:border-orange-300' }}">
                                @if ($package['bonus'] > 0)
                                    <div class="absolute -top-1 -right-1 bg-green-500 text-white text-xs font-bold px-1 py-0.5 rounded-full">
                                        +{{ number_format($package['bonus']) }}
                                    </div>
                                @endif
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-900">{{ number_format($package['credits']) }}</div>
                                    <div class="text-xs text-gray-500">Credits</div>
                                    @if ($package['bonus'] > 0)
                                        <div class="text-xs text-green-600 font-medium mt-1">Total: {{ number_format($package['total_credits']) }}</div>
                                    @endif
                                    <div class="mt-2 text-base font-bold text-orange-600">₦{{ number_format($package['amount']) }}</div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>

                <!-- Custom Amount Input -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Or enter custom amount (Credits)</label>
                    <input type="number" wire:model.live="customCreditAmount" class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" placeholder="Minimum {{ number_format($minCreditAmount) }} credits" min="{{ $minCreditAmount }}">
                    @error('customCreditAmount')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    @if ($customCreditAmount && $customCreditAmount >= $minCreditAmount)
                        <div class="text-sm text-gray-600 mt-1">Cost: ₦{{ number_format($customCreditAmount * ($pricingConfig['credit_price'] ?? 1), 2) }}</div>
                    @endif
                </div>

                <!-- Purchase Button -->
                <button wire:click="purchaseCreditsWithNaira" wire:loading.attr="disabled" class="w-full bg-gradient-to-r from-orange-500 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50">
                    <span wire:loading.remove wire:target="purchaseCreditsWithNaira">Purchase Credits</span>
                    <span wire:loading wire:target="purchaseCreditsWithNaira" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </div>
        </div>

        <!-- Withdrawal Modal -->
        <div x-show="$wire.showWithdrawModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center p-4 z-50" @click.self="$wire.closeWithdrawModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-1 transform translate-y-0 sm:scale-100" class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Withdraw Earnings</h3>
                    <button wire:click="closeWithdrawModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Withdrawal Methods -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer" @click="$wire.set('withdrawalMethod', 'bank_account')">
                            <input type="radio" wire:model.live="withdrawalMethod" value="bank_account" class="mr-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span class="font-medium">Bank Transfer</span>
                            </div>
                        </div>

                        <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer" @click="$wire.set('withdrawalMethod', 'palmpay')">
                            <input type="radio" wire:model.live="withdrawalMethod" value="palmpay" class="mr-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span class="font-medium">PalmPay</span>
                            </div>
                        </div>

                        @if ($airtimeApiEnabled)
                            <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer" @click="$wire.set('withdrawalMethod', 'airtime')">
                                <input type="radio" wire:model.live="withdrawalMethod" value="airtime" class="mr-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank</label>
                                <select wire:model.live="bankCode" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <option value="">Select Bank</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('bankCode')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                                <input type="text" wire:model.live="accountNumber" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Enter account number" maxlength="10">
                                @error('accountNumber')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($accountName)
                                <div class="text-sm text-green-600 bg-green-50 p-2 rounded">✓ {{ $accountName }}</div>
                            @endif
                        </div>
                    @elseif($withdrawalMethod === 'palmpay')
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">PalmPay Number</label>
                                <input type="tel" wire:model.live="palmpayNumber" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Enter PalmPay phone number" maxlength="11">
                                @error('palmpayNumber')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @elseif($withdrawalMethod === 'airtime')
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" wire:model.live="airtimeNumber" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Enter phone number" maxlength="11">
                                @error('airtimeNumber')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($detectedNetwork)
                                <div class="text-sm text-blue-600 bg-blue-50 p-2 rounded">Detected: {{ $detectedNetwork }}</div>
                            @endif
                            @if ($networks && count($networks) > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Network</label>
                                    <select wire:model.live="airtimeNetwork" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        <option value="">Select Network</option>
                                        @foreach ($networks as $networkId => $networkName)
                                            <option value="{{ $networkId }}">{{ $networkName }}</option>
                                        @endforeach
                                    </select>
                                    @error('airtimeNetwork')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Amount Field -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₦)</label>
                        <input type="number" wire:model.live="amount" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500" placeholder="Enter amount" min="1000">
                        @error('amount')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        @if (isset($fees['total']) && $fees['total'] > 0)
                            <div class="text-sm text-gray-600 mt-1">Fee: ₦{{ number_format($fees['total'], 2) }}</div>
                            <div class="text-sm font-medium text-gray-900">You'll receive: ₦{{ number_format($netAmount, 2) }}</div>
                        @endif
                        <div class="text-xs text-gray-500 mt-1">Minimum: ₦1,000</div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex space-x-3">
                        <button wire:click="closeWithdrawModal" class="flex-1 bg-gray-100 text-gray-800 py-3 px-4 rounded-xl hover:bg-gray-200 transition-colors active:scale-95">Cancel</button>
                        <button wire:click="processWithdrawal" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-green-500 to-teal-600 text-white py-3 px-4 rounded-xl hover:shadow-lg transition-all active:scale-95 disabled:opacity-50">
                            <span wire:loading.remove wire:target="processWithdrawal">Withdraw</span>
                            <span wire:loading wire:target="processWithdrawal" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fund Wallet Modal -->
        <div x-show="$wire.showFundModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center p-4 z-50" @click.self="$wire.closeFundModal()">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-1 transform translate-y-0 sm:scale-100" class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-xl font-bold text-gray-900">Fund Naira Wallet</h3>
                    <button wire:click="closeFundModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Amount Field -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount (₦)</label>
                        <input type="number" wire:model.live="fundAmount" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-lg py-3 px-4" placeholder="Enter amount to fund" min="{{ $pricingConfig['minimum_amount'] ?? 100 }}">
                        @error('fundAmount')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        <div class="text-xs text-gray-500 mt-1">Minimum: ₦{{ number_format($pricingConfig['minimum_amount'] ?? 100) }}</div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-blue-50 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">Payment via Paystack</p>
                                <p>You will be redirected to Paystack to complete your payment securely.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex space-x-3">
                        <button wire:click="closeFundModal" class="flex-1 bg-gray-100 text-gray-800 py-3 px-4 rounded-xl hover:bg-gray-200 transition-colors active:scale-95">Cancel</button>
                        <button wire:click="fundWallet" wire:loading.attr="disabled" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-4 rounded-xl hover:shadow-lg transition-all active:scale-95 disabled:opacity-50">
                            <span wire:loading.remove wire:target="fundWallet">Proceed to Payment</span>
                            <span wire:loading wire:target="fundWallet" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
                transition: all 0.2s ease;
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
                background: #f1f1f1;
                border-radius: 2px;
            }
            
            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 2px;
            }
            
            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #a1a1a1;
            }
            
            /* Enhanced button press effects */
            .active\:scale-95:active {
                transform: scale(0.95);
            }
            
            .active\:scale-98:active {
                transform: scale(0.98);
            }
            
            /* Responsive text sizing */
            @media (max-width: 480px) {
                .text-3xl {
                    font-size: 1.75rem;
                    line-height: 2rem;
                }
                
                .text-4xl {
                    font-size: 2rem;
                    line-height: 2.25rem;
                }
            }
            
            /* Backdrop blur fallback */
            @supports not (backdrop-filter: blur(12px)) {
                .backdrop-blur-md {
                    background-color: rgba(255, 255, 255, 0.9);
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://js.paystack.co/v1/inline.js"></script>
        <script>
            function walletApp() {
                return {
                    showFlash: true,
                    showCreditSection: false,
                    
                    init() {
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
                                    navigator.vibrate(50);
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