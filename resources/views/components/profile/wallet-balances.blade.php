<!-- Wallet Balances Component -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <!-- Credits Card -->
    <div class="wallet-card bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 sm:p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <span class="text-xs sm:text-sm font-medium opacity-90">Credits</span>
        </div>
        <div class="text-2xl sm:text-3xl font-bold mb-1">{{ number_format($user->credits) }}</div>
        <div class="text-xs sm:text-sm opacity-75">Available Credits</div>
    </div>

    <!-- Naira Balance Card -->
    <div class="wallet-card bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 sm:p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <span class="text-xs sm:text-sm font-medium opacity-90">Naira</span>
        </div>
        <div class="text-2xl sm:text-3xl font-bold mb-1">₦{{ number_format($user->getNairaWallet()->balance, 2) }}</div>
        <div class="text-xs sm:text-sm opacity-75">Wallet Balance</div>
    </div>

    <!-- Earnings Card -->
    <div class="wallet-card bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 sm:p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl sm:col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <span class="text-xs sm:text-sm font-medium opacity-90">Earnings</span>
        </div>
        <div class="text-2xl sm:text-3xl font-bold mb-1">₦{{ number_format($user->total_earnings, 2) }}</div>
        <div class="text-xs sm:text-sm opacity-75">Total Earned</div>
    </div>
</div>