<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Withdraw Earnings</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Request withdrawal from your earnings balance to your preferred payment method</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Current Earnings Balance -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium">Available Earnings</h3>
                <p class="text-3xl font-bold mt-2">₦{{ number_format($user->earnings_balance, 2) }}</p>
                <p class="text-green-100 mt-1">Ready for withdrawal</p>
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

    <!-- Withdrawal Information -->
    <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-8">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Withdrawal Information</h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <p>• Minimum withdrawal: ₦{{ number_format(\App\Livewire\Withdrawal::MINIMUM_WITHDRAWAL) }}</p>
                    <p>• Bank transfers require BVN verification</p>
                    <p>• Processing time: Within 24 hours</p>
                    <p>• All withdrawals require admin approval</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Form -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Withdrawal Details</h3>
        
        <!-- Amount Input -->
        <div class="mb-6">
            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Withdrawal Amount (₦)
            </label>
            <input wire:model.live.debounce.300ms="amount" 
                   type="number" 
                   id="amount" 
                   min="{{ \App\Livewire\Withdrawal::MINIMUM_WITHDRAWAL }}" 
                   step="1"
                   max="{{ $user->earnings_balance }}"
                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                   placeholder="Enter amount to withdraw">
            @error('amount')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Withdrawal Method Selection -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                Withdrawal Method
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Bank Account -->
                <div class="relative">
                    <input type="radio" 
                           wire:model.live="withdrawalMethod" 
                           value="bank_account"
                           name="withdrawal_method" 
                           id="method-bank" 
                           class="sr-only peer">
                    <label for="method-bank" 
                           class="block p-4 border-2 rounded-lg cursor-pointer transition-all duration-200
                                  {{ $withdrawalMethod === 'bank_account' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500' }}">
                        <div class="text-center">
                            <div class="w-8 h-8 mx-auto mb-2 text-indigo-600 dark:text-indigo-400">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                            </div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Bank Account</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">1.5% + ₦100</div>
                        </div>
                    </label>
                </div>

                <!-- Opay -->
                <div class="relative">
                    <input type="radio" 
                           wire:model.live="withdrawalMethod" 
                           value="opay"
                           name="withdrawal_method" 
                           id="method-opay" 
                           class="sr-only peer">
                    <label for="method-opay" 
                           class="block p-4 border-2 rounded-lg cursor-pointer transition-all duration-200
                                  {{ $withdrawalMethod === 'opay' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500' }}">
                        <div class="text-center">
                            <div class="w-8 h-8 mx-auto mb-2 text-indigo-600 dark:text-indigo-400">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                            </div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Opay</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">1.0%</div>
                        </div>
                    </label>
                </div>

                <!-- PalmPay -->
                <div class="relative">
                    <input type="radio" 
                           wire:model.live="withdrawalMethod" 
                           value="palmpay"
                           name="withdrawal_method" 
                           id="method-palmpay" 
                           class="sr-only peer">
                    <label for="method-palmpay" 
                           class="block p-4 border-2 rounded-lg cursor-pointer transition-all duration-200
                                  {{ $withdrawalMethod === 'palmpay' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500' }}">
                        <div class="text-center">
                            <div class="w-8 h-8 mx-auto mb-2 text-indigo-600 dark:text-indigo-400">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                            </div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">PalmPay</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">1.0%</div>
                        </div>
                    </label>
                </div>

                <!-- Airtime -->
                <div class="relative">
                    <input type="radio" 
                           wire:model.live="withdrawalMethod" 
                           value="airtime"
                           name="withdrawal_method" 
                           id="method-airtime" 
                           class="sr-only peer">
                    <label for="method-airtime" 
                           class="block p-4 border-2 rounded-lg cursor-pointer transition-all duration-200
                                  {{ $withdrawalMethod === 'airtime' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500' }}">
                        <div class="text-center">
                            <div class="w-8 h-8 mx-auto mb-2 text-indigo-600 dark:text-indigo-400">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                            </div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Airtime</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">2.0%</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Method-specific Fields -->
        @if($withdrawalMethod === 'bank_account')
            <div class="space-y-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="bankCode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Bank
                        </label>
                        <select wire:model.live="bankCode" 
                                id="bankCode" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Bank</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                            @endforeach
                        </select>
                        @error('bankCode')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="accountNumber" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Account Number
                        </label>
                        <input wire:model.live.debounce.500ms="accountNumber" 
                               type="text" 
                               id="accountNumber" 
                               maxlength="10"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               placeholder="Enter 10-digit account number">
                        @error('accountNumber')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                @if($accountName)
                    <div class="p-3 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-md">
                        <p class="text-sm text-green-800 dark:text-green-200">
                            <span class="font-medium">Account Name:</span> {{ $accountName }}
                        </p>
                    </div>
                @endif
            </div>
        @endif

        @if($withdrawalMethod === 'opay')
            <div class="mb-6">
                <label for="opayNumber" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Opay Phone Number
                </label>
                <input wire:model.live="opayNumber" 
                       type="text" 
                       id="opayNumber" 
                       maxlength="11"
                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                       placeholder="Enter 11-digit Opay number">
                @error('opayNumber')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endif

        @if($withdrawalMethod === 'palmpay')
            <div class="mb-6">
                <label for="palmpayNumber" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    PalmPay Phone Number
                </label>
                <input wire:model.live="palmpayNumber" 
                       type="text" 
                       id="palmpayNumber" 
                       maxlength="11"
                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                       placeholder="Enter 11-digit PalmPay number">
                @error('palmpayNumber')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endif

        @if($withdrawalMethod === 'airtime')
            <div class="space-y-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="airtimeNetwork" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Network
                        </label>
                        <select wire:model.live="airtimeNetwork" 
                                id="airtimeNetwork" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Network</option>
                            @foreach($networks as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('airtimeNetwork')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="airtimeNumber" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Phone Number
                        </label>
                        <input wire:model.live="airtimeNumber" 
                               type="text" 
                               id="airtimeNumber" 
                               maxlength="11"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               placeholder="Enter 11-digit phone number">
                        @error('airtimeNumber')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        <!-- Fee Breakdown -->
        @if($amount >= \App\Livewire\Withdrawal::MINIMUM_WITHDRAWAL && !empty($fees))
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Fee Breakdown</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Withdrawal Amount:</span>
                        <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($amount, 2) }}</span>
                    </div>
                    
                    @if(isset($fees['percentage']))
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Processing Fee ({{ $withdrawalMethod === 'bank_account' ? '1.5%' : ($withdrawalMethod === 'airtime' ? '2.0%' : '1.0%') }}):</span>
                            <span class="text-red-600 dark:text-red-400">-₦{{ number_format($fees['percentage'], 2) }}</span>
                        </div>
                    @endif
                    
                    @if(isset($fees['fixed']))
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Fixed Fee:</span>
                            <span class="text-red-600 dark:text-red-400">-₦{{ number_format($fees['fixed'], 2) }}</span>
                        </div>
                    @endif
                    
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-2">
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-900 dark:text-white">Total Fees:</span>
                            <span class="font-medium text-red-600 dark:text-red-400">-₦{{ number_format($fees['total'], 2) }}</span>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span class="font-medium text-gray-900 dark:text-white">You'll Receive:</span>
                            <span class="font-bold text-green-600 dark:text-green-400">₦{{ number_format($netAmount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button wire:click="processWithdrawal" 
                    wire:loading.attr="disabled"
                    @if($isProcessing || $amount < \App\Livewire\Withdrawal::MINIMUM_WITHDRAWAL || empty($withdrawalMethod)) disabled @endif
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="processWithdrawal">Submit Withdrawal Request</span>
                <span wire:loading wire:target="processWithdrawal" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </span>
            </button>
        </div>
    </div>

    <!-- BVN Modal -->
    @if($showBvnModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="bvn-modal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">BVN Verification Required</h3>
                        <button wire:click="$set('showBvnModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Bank transfers require BVN verification for security purposes. Your BVN will be encrypted and stored securely.
                    </p>
                    
                    <div class="mb-4">
                        <label for="bvn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Bank Verification Number (BVN)
                        </label>
                        <input wire:model.live="bvn" 
                               type="text" 
                               id="bvn" 
                               maxlength="11"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               placeholder="Enter your 11-digit BVN">
                        @error('bvn')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button wire:click="$set('showBvnModal', false)" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-md">
                            Cancel
                        </button>
                        <button wire:click="saveBvnAndContinue" 
                                wire:loading.attr="disabled"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveBvnAndContinue">Verify & Continue</span>
                            <span wire:loading wire:target="saveBvnAndContinue">Verifying...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>