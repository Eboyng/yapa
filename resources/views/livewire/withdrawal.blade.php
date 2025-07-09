<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

        <!-- Flash Messages -->
        @if (session()->has('success') || session()->has('error'))
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
            </div>
        @endif

        <!-- Balance & Main Content Grid -->
        <div class="lg:grid lg:grid-cols-5 lg:gap-8">
            
            <!-- Left Column: Withdrawal Form -->
            <div class="lg:col-span-3">
                <!-- Balance Card -->
                <div class="bg-gradient-to-r from-orange-500 to-purple-600 rounded-2xl p-6 mb-8 text-white shadow-xl hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium opacity-90 mb-1">Available Earnings</h3>
                            <p class="text-3xl font-bold">₦{{ number_format($user->getEarningsWallet()->balance, 2) }}</p>
                        </div>
                        <div class="bg-white/20 rounded-xl p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Withdrawal Form Card -->
                <div class="bg-white/80 backdrop-blur-sm shadow-lg rounded-2xl p-6 lg:p-8 border border-gray-100">
                    <h3 class="text-xl font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent mb-6">Withdrawal Details</h3>
                    
                    <!-- Form Content -->
                    <div class="space-y-6">
                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">Amount</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">₦</span>
                                <input wire:model.live.debounce.300ms="amount" type="number" id="amount" min="{{ \App\Livewire\Withdrawal::MINIMUM_WITHDRAWAL }}" step="1" max="{{ $user->getEarningsWallet()->balance }}" class="w-full pl-9 pr-4 py-3 rounded-xl border-gray-200 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition" placeholder="0.00">
                            </div>
                            @error('amount') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Withdrawal Method -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Withdrawal Method</label>
                            <div class="grid grid-cols-2 gap-3">
                                @php
                                    $methods = [
                                        'bank_account' => ['name' => 'Bank', 'fee' => '1.5% + ₦100', 'icon' => '<path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>'],
                                        'opay' => ['name' => 'Opay', 'fee' => '1.0%', 'icon' => '<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>'],
                                        'palmpay' => ['name' => 'PalmPay', 'fee' => '1.0%', 'icon' => '<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>'],
                                        'airtime' => ['name' => 'Airtime', 'fee' => '2.0%', 'icon' => '<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>']
                                    ];
                                @endphp
                                @foreach($methods as $key => $method)
                                <div class="relative">
                                    <input type="radio" wire:model.live="withdrawalMethod" value="{{ $key }}" id="method-{{ $key }}" class="sr-only peer">
                                    <label for="method-{{ $key }}" class="block p-3 border-2 rounded-xl cursor-pointer transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300">
                                        <div class="text-center">
                                            <svg class="w-7 h-7 mx-auto mb-1 text-orange-600" fill="currentColor" viewBox="0 0 20 20">{!! $method['icon'] !!}</svg>
                                            <p class="text-sm font-bold text-gray-900">{{ $method['name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $method['fee'] }}</p>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Method-specific Fields -->
                        <div class="animate-slideDown">
                            @if($withdrawalMethod === 'bank_account')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="bankCode" class="block text-sm font-semibold text-gray-700 mb-2">Bank</label>
                                        <select wire:model.live="bankCode" id="bankCode" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition">
                                            <option value="">Select Bank</option>
                                            @foreach($banks as $bank) <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option> @endforeach
                                        </select>
                                        @error('bankCode') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="accountNumber" class="block text-sm font-semibold text-gray-700 mb-2">Account Number</label>
                                        <input wire:model.live.debounce.500ms="accountNumber" type="text" id="accountNumber" maxlength="10" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition" placeholder="10-digit number">
                                        @error('accountNumber') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                @if($accountName)
                                    <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-xl text-center">
                                        <p class="text-sm font-medium text-green-800">{{ $accountName }}</p>
                                    </div>
                                @endif
                            @elseif(in_array($withdrawalMethod, ['opay', 'palmpay', 'airtime']))
                                <div>
                                    <label for="phoneNumber" class="block text-sm font-semibold text-gray-700 mb-2">{{ ucfirst($withdrawalMethod) }} Phone Number</label>
                                    <input wire:model.live="{{ $withdrawalMethod }}Number" type="text" id="phoneNumber" maxlength="11" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition" placeholder="11-digit number">
                                    @error($withdrawalMethod.'Number') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                @if($withdrawalMethod === 'airtime')
                                    <div class="mt-4">
                                        <label for="airtimeNetwork" class="block text-sm font-semibold text-gray-700 mb-2">Network</label>
                                        <select wire:model.live="airtimeNetwork" id="airtimeNetwork" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition">
                                            <option value="">Select Network</option>
                                            @foreach($networks as $code => $name) <option value="{{ $code }}">{{ $name }}</option> @endforeach
                                        </select>
                                        @error('airtimeNetwork') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Fee Breakdown -->
                        @if($amount >= \App\Livewire\Withdrawal::MINIMUM_WITHDRAWAL && !empty($fees))
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 space-y-2 text-sm animate-slideDown">
                                <div class="flex justify-between items-center"><span class="text-gray-600">Amount:</span><span class="font-medium text-gray-900">₦{{ number_format($amount, 2) }}</span></div>
                                <div class="flex justify-between items-center"><span class="text-gray-600">Total Fees:</span><span class="font-medium text-red-600">- ₦{{ number_format($fees['total'], 2) }}</span></div>
                                <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between items-center"><span class="font-semibold text-gray-900">You'll Receive:</span><span class="font-bold text-green-600 text-base">₦{{ number_format($netAmount, 2) }}</span></div>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <button wire:click="processWithdrawal" wire:loading.attr="disabled" @if($isProcessing || $amount < \App\Livewire\Withdrawal::MINIMUM_WITHDRAWAL || empty($withdrawalMethod)) disabled @endif
                                class="w-full bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105">
                            <span wire:loading.remove wire:target="processWithdrawal">Submit Withdrawal Request</span>
                            <span wire:loading wire:target="processWithdrawal" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Information -->
            <div class="lg:col-span-2 mt-8 lg:mt-0">
                <div class="lg:sticky lg:top-12">
                    <div class="bg-white/80 backdrop-blur-sm border border-gray-100 rounded-2xl p-6 shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            Withdrawal Information
                        </h3>
                        <div class="text-sm text-gray-700 space-y-3">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mr-3 mt-0.5 text-orange-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path></svg>
                                <span>Minimum withdrawal amount is <strong>₦{{ number_format(\App\Livewire\Withdrawal::MINIMUM_WITHDRAWAL) }}</strong>.</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mr-3 mt-0.5 text-purple-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                <span>Withdrawal requests are typically processed within <strong>24 hours</strong>.</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mr-3 mt-0.5 text-orange-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span>Bank transfers require <strong>BVN verification</strong> for security purposes.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BVN Modal -->
        @if($showBvnModal)
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fadeIn">
                <div class="bg-white rounded-2xl p-6 lg:p-8 w-full max-w-md mx-auto shadow-2xl animate-slideUp">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">BVN Required</h3>
                        <button wire:click="$set('showBvnModal', false)" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mb-6">For security, we need to verify your BVN for bank transfers. Your information is encrypted and secure.</p>
                    <div class="space-y-4">
                        <div>
                            <label for="bvn" class="block text-sm font-semibold text-gray-700 mb-2">Bank Verification Number (BVN)</label>
                            <input wire:model.live="bvn" type="text" id="bvn" maxlength="11" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 transition" placeholder="Enter your 11-digit BVN">
                            @error('bvn') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <button wire:click="saveBvnAndContinue" wire:loading.attr="disabled" class="w-full bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveBvnAndContinue">Verify & Continue</span>
                            <span wire:loading wire:target="saveBvnAndContinue">Verifying...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <style>
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .animate-slideDown { animation: slideDown 0.3s ease-out forwards; }
    .animate-slideUp { animation: slideUp 0.3s ease-out forwards; }
    .animate-fadeIn { animation: fadeIn 0.2s ease-out forwards; }
</style>
</div>

