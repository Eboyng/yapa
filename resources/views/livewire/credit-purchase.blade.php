<div class="">
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
        <div class="max-w-md mx-auto px-4 py-6">

            <!-- Flash Messages -->
            @if (session()->has('success') || session()->has('error') || session()->has('info'))
                <div class="mb-4 animate-slideDown">
                    @if (session()->has('success'))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('info'))
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-blue-800">{{ session('info') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Wallet Cards -->
            <div class="space-y-4 mb-6">
                <!-- Naira Wallet Card -->
                <div
                    class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-5 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                    <!-- Card Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium opacity-90 ml-2">Naira Wallet</h3>
                        </div>
                        <div class="text-xs opacity-75">Available</div>
                    </div>

                    <!-- Balance Display -->
                    <div class="mb-4">
                        <div class="text-2xl font-bold">₦{{ number_format($user->getNairaWallet()->balance, 2) }}</div>
                        <div class="text-white/70 text-sm">Ready for transactions</div>
                    </div>

                    <!-- Fund Button -->
                    <button
                        class="w-full bg-white/20 backdrop-blur-sm text-white font-medium py-3 px-4 rounded-xl hover:bg-white/30 transition-all duration-200 active:scale-95 flex items-center justify-center group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Fund Wallet
                    </button>
                </div>

                <!-- Credits Wallet Card (Read-only) -->
                <div
                    class="bg-gradient-to-r from-orange-500 to-purple-600 rounded-2xl p-5 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                    <!-- Card Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium opacity-90 ml-2">Credits</h3>
                        </div>
                        <div class="text-xs opacity-75">Messaging</div>
                    </div>

                    <!-- Balance Display -->
                    <div class="mb-4">
                        <div class="text-2xl font-bold">{{ number_format($user->getCreditWallet()->balance) }}</div>
                        <div class="text-white/70 text-sm">For WhatsApp messaging</div>
                    </div>

                    <!-- Info Button -->
                    <div class="w-full bg-white/10 backdrop-blur-sm text-white/70 font-medium py-3 px-4 rounded-xl text-center text-sm">
                        💡 Credits earned from channel ads
                    </div>
                </div>

                <!-- Earnings Wallet Card -->
                <div
                    class="bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl p-5 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                    <!-- Card Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium opacity-90 ml-2">Earnings</h3>
                        </div>
                        <div class="text-xs opacity-75">Withdrawable</div>
                    </div>

                    <!-- Balance Display -->
                    <div class="mb-4">
                        <div class="text-2xl font-bold">₦{{ number_format($user->getEarningsWallet()->balance, 2) }}
                        </div>
                        <div class="text-white/70 text-sm">Ready to withdraw</div>
                    </div>

                    <!-- Withdraw Button -->
                    <button wire:click="openWithdrawModal"
                        class="w-full bg-white/20 backdrop-blur-sm text-white font-medium py-3 px-4 rounded-xl hover:bg-white/30 transition-all duration-200 active:scale-95 flex items-center justify-center group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                        Withdraw
                    </button>
                </div>
            </div>

            <!-- Naira Wallet Funding Section -->
            <div class="bg-white rounded-2xl p-5 mb-6 shadow-lg">
                <div class="flex items-center mb-4">
                    <div
                        class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Fund Naira Wallet</h3>
                </div>

                <!-- Pricing Info -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <div class="text-sm text-gray-700 space-y-1">
                        <div>💳 Secure payment via Paystack</div>
                        <div>⚡ Instant wallet funding</div>
                        <div class="text-blue-600 font-medium">🎁 Bonus Naira on large packages!</div>
                    </div>
                </div>

                <!-- Package Selection -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    @foreach ($packages as $index => $package)
                        <div class="relative">
                            <input type="radio" wire:click="selectPackage({{ $index }})" name="package"
                                id="package-{{ $index }}" class="sr-only peer">
                            <label for="package-{{ $index }}"
                                class="block p-3 border-2 rounded-lg cursor-pointer transition-all duration-200 hover:scale-105 active:scale-95
                                      {{ $selectedPackage === $index ? 'border-blue-500 bg-gradient-to-br from-blue-50 to-indigo-50 shadow-md' : 'border-gray-200 hover:border-blue-300' }}">

                                @if ($package['bonus'] > 0)
                                    <div
                                        class="absolute -top-1 -right-1 bg-green-500 text-white text-xs font-bold px-1 py-0.5 rounded-full">
                                        +₦{{ number_format($package['bonus']) }}
                                    </div>
                                @endif

                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-900">
                                        ₦{{ number_format($package['naira']) }}
                                    </div>
                                    <div class="text-xs text-gray-500">Base Amount</div>

                                    @if ($package['bonus'] > 0)
                                        <div class="text-xs text-green-600 font-medium mt-1">
                                            Total: ₦{{ number_format($package['total_naira']) }}
                                        </div>
                                    @endif

                                    <div class="mt-2 text-base font-bold text-blue-600">
                                        Pay ₦{{ number_format($package['amount']) }}
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>

                <!-- Fund Button -->
                <button wire:click="purchaseCredits" wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50">
                    <span wire:loading.remove>Fund Wallet</span>
                    <span wire:loading class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </div>

            <!-- Withdrawal Modal -->
            @if ($showWithdrawModal)
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-end justify-center p-4 z-50 animate-fadeIn"
                    wire:click.self="closeWithdrawModal">
                    <div class="bg-white rounded-t-2xl w-full max-w-md max-h-[90vh] overflow-y-auto animate-slideUp">
                        <!-- Modal Header -->
                        <div class="flex justify-between items-center p-5 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Withdraw Earnings</h3>
                            <button wire:click="closeWithdrawModal"
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="p-5">
                            <!-- Withdrawal Methods -->
                            <div class="space-y-3 mb-5">
                                <div
                                    class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition-colors">
                                    <input type="radio" wire:model.live="withdrawalMethod" value="bank_account"
                                        class="mr-3">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                            </path>
                                        </svg>
                                        <span class="font-medium">Bank Transfer</span>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition-colors">
                                    <input type="radio" wire:model.live="withdrawalMethod" value="palmpay"
                                        class="mr-3">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span class="font-medium">PalmPay</span>
                                    </div>
                                </div>

                                @if ($airtimeApiEnabled)
                                    <div
                                        class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition-colors">
                                        <input type="radio" wire:model.live="withdrawalMethod" value="airtime"
                                            class="mr-3">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-purple-500 mr-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0">
                                                </path>
                                            </svg>
                                            <span class="font-medium">Airtime</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Method-specific Fields -->
                            @if ($withdrawalMethod === 'bank_account')
                                <div class="space-y-4 mb-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank</label>
                                        <select wire:model.live="bankCode"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
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
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Account
                                            Number</label>
                                        <input type="text" wire:model.live="accountNumber"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            placeholder="Enter account number">
                                        @error('accountNumber')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    @if ($accountName)
                                        <div class="text-sm text-green-600 bg-green-50 p-2 rounded">✓
                                            {{ $accountName }}
                                        </div>
                                    @endif
                                </div>
                            @elseif($withdrawalMethod === 'palmpay')
                                <div class="space-y-4 mb-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">PalmPay
                                            Number</label>
                                        <input type="tel" wire:model.live="palmpayNumber"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            placeholder="Enter PalmPay phone number">
                                        @error('palmpayNumber')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @elseif($withdrawalMethod === 'airtime')
                                <div class="space-y-4 mb-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone
                                            Number</label>
                                        <input type="tel" wire:model.live="airtimeNumber"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            placeholder="Enter phone number">
                                        @error('airtimeNumber')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    @if ($detectedNetwork)
                                        <div class="text-sm text-blue-600 bg-blue-50 p-2 rounded">Detected:
                                            {{ $detectedNetwork }}</div>
                                    @endif
                                    @if ($networks && count($networks) > 0)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Network</label>
                                            <select wire:model.live="airtimeNetwork"
                                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
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
                                <input type="number" wire:model.live="amount"
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                    placeholder="Enter amount" min="1000">
                                @error('amount')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                @if (isset($fees['total']) && $fees['total'] > 0)
                                    <div class="text-sm text-gray-600 mt-1">Fee:
                                        ₦{{ number_format($fees['total'], 2) }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">You'll receive:
                                        ₦{{ number_format($netAmount, 2) }}</div>
                                @endif
                                <div class="text-xs text-gray-500 mt-1">Minimum: ₦1,000</div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex space-x-3">
                                <button wire:click="closeWithdrawModal"
                                    class="flex-1 bg-gray-100 text-gray-800 py-3 px-4 rounded-xl hover:bg-gray-200 transition-colors active:scale-95">
                                    Cancel
                                </button>
                                <button wire:click="processWithdrawal" wire:loading.attr="disabled"
                                    class="flex-1 bg-gradient-to-r from-green-500 to-teal-600 text-white py-3 px-4 rounded-xl hover:shadow-lg transition-all active:scale-95 disabled:opacity-50">
                                    <span wire:loading.remove>Withdraw</span>
                                    <span wire:loading class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Processing...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Transactions -->
            <div class="bg-white rounded-2xl shadow-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Recent Activity</h2>
                    <button class="text-sm text-gray-500 hover:text-gray-700">View All</button>
                </div>

                <div class="space-y-3">
                    @forelse($user->transactions()->latest()->take(5)->get() as $transaction)
                        <div
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center mr-3 {{ $transaction->type == 'credit' ? 'bg-green-100' : 'bg-orange-100' }}">
                                    @if ($transaction->type == 'credit')
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 text-sm">{{ ucfirst($transaction->type) }}
                                        {{ ucfirst($transaction->method ?? 'Transaction') }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $transaction->created_at->format('M j, g:i A') }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div
                                    class="font-semibold text-sm {{ $transaction->type == 'credit' ? 'text-green-600' : 'text-orange-600' }}">
                                    {{ $transaction->type == 'credit' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                </div>
                                <div class="text-xs text-gray-500 capitalize">{{ $transaction->status }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div
                                class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-sm">No transactions yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton Loader Component -->
    <div wire:loading class="fixed inset-0 bg-white z-50 flex items-center justify-center">
        <div class="max-w-md mx-auto px-4 w-full">
            <div class="space-y-4 mb-6">
                <!-- Skeleton Credit Card -->
                <div class="bg-gray-200 rounded-2xl p-5 animate-pulse">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-300 rounded-lg mr-2"></div>
                            <div class="h-4 bg-gray-300 rounded w-16"></div>
                        </div>
                        <div class="h-3 bg-gray-300 rounded w-12"></div>
                    </div>
                    <div class="mb-4">
                        <div class="h-8 bg-gray-300 rounded w-24 mb-2"></div>
                        <div class="h-4 bg-gray-300 rounded w-20"></div>
                    </div>
                    <div class="h-10 bg-gray-300 rounded-xl"></div>
                </div>

                <!-- Skeleton Earnings Card -->
                <div class="bg-gray-200 rounded-2xl p-5 animate-pulse">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-300 rounded-lg mr-2"></div>
                            <div class="h-4 bg-gray-300 rounded w-16"></div>
                        </div>
                        <div class="h-3 bg-gray-300 rounded w-16"></div>
                    </div>
                    <div class="mb-4">
                        <div class="h-8 bg-gray-300 rounded w-24 mb-2"></div>
                        <div class="h-4 bg-gray-300 rounded w-20"></div>
                    </div>
                    <div class="h-10 bg-gray-300 rounded-xl"></div>
                </div>
            </div>

            <!-- Skeleton Purchase Section -->
            <div class="bg-gray-200 rounded-2xl p-5 mb-6 animate-pulse">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-gray-300 rounded-lg mr-3"></div>
                    <div class="h-5 bg-gray-300 rounded w-24"></div>
                </div>
                <div class="h-16 bg-gray-300 rounded-lg mb-4"></div>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="h-20 bg-gray-300 rounded-lg"></div>
                    <div class="h-20 bg-gray-300 rounded-lg"></div>
                    <div class="h-20 bg-gray-300 rounded-lg"></div>
                    <div class="h-20 bg-gray-300 rounded-lg"></div>
                </div>
                <div class="h-12 bg-gray-300 rounded-xl"></div>
            </div>

            <!-- Skeleton Transactions -->
            <div class="bg-gray-200 rounded-2xl p-5 animate-pulse">
                <div class="h-5 bg-gray-300 rounded w-32 mb-4"></div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-300 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-400 rounded-full mr-3"></div>
                            <div>
                                <div class="h-4 bg-gray-400 rounded w-24 mb-1"></div>
                                <div class="h-3 bg-gray-400 rounded w-16"></div>
                            </div>
                        </div>
                        <div class="h-4 bg-gray-400 rounded w-16"></div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-300 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-400 rounded-full mr-3"></div>
                            <div>
                                <div class="h-4 bg-gray-400 rounded w-24 mb-1"></div>
                                <div class="h-3 bg-gray-400 rounded w-16"></div>
                            </div>
                        </div>
                        <div class="h-4 bg-gray-400 rounded w-16"></div>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                @keyframes slideDown {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                    }

                    to {
                        opacity: 1;
                    }
                }

                .animate-slideDown {
                    animation: slideDown 0.3s ease-out;
                }

                .animate-slideUp {
                    animation: slideUp 0.3s ease-out;
                }

                .animate-fadeIn {
                    animation: fadeIn 0.3s ease-out;
                }

                /* Smooth transitions for interactive elements */
                button,
                input,
                select {
                    transition: all 0.2s ease;
                }

                /* Custom scrollbar for mobile */
                .overflow-y-auto::-webkit-scrollbar {
                    width: 4px;
                }

                .overflow-y-auto::-webkit-scrollbar-track {
                    background: #f1f1f1;
                }

                .overflow-y-auto::-webkit-scrollbar-thumb {
                    background: #c1c1c1;
                    border-radius: 2px;
                }

                .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                    background: #a1a1a1;
                }
            </style>
        @endpush

        @push('scripts')
            <script src="https://js.paystack.co/v1/inline.js"></script>
            <script>
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
                                    value: "credit_purchase"
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

                    // Add haptic feedback for mobile devices
                    if ('vibrate' in navigator) {
                        document.addEventListener('click', function(e) {
                            if (e.target.closest('button')) {
                                navigator.vibrate(50);
                            }
                        });
                    }
                });
            </script>
        @endpush
    </div>

</div>
