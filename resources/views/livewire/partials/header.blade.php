@php
    $user = auth()->user();
    $unreadNotifications = $user ? $user->unreadNotifications()->count() : 0;
    $settingService = app(\App\Services\SettingService::class);
    $brandingSettings = $settingService->getBrandingSettings();
    $siteName = $brandingSettings['site_name'] ?? config('app.name', 'Laravel');
    $logoText = $brandingSettings['logo_text'] ?? $siteName;
@endphp

<div>
    <!-- Header Component -->
    <header class="bg-white/95 backdrop-blur-md border-b border-gray-100 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 sm:space-x-3 group">
                        @if($brandingSettings['site_logo'])
                            <img src="{{ asset('storage/' . $brandingSettings['site_logo']) }}" alt="{{ $siteName }}" class="w-8 h-8 sm:w-10 sm:h-10 object-contain shadow-lg group-hover:shadow-xl transition-all duration-200 group-hover:scale-105">
                        @else
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-orange-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-200 group-hover:scale-105">
                                <span class="text-white text-sm sm:text-base ">{{ substr($siteName, 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="text-lg sm:text-xl  bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent hidden sm:block">{{ $logoText }}</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-1">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:bg-orange-50 {{ request()->routeIs('home') ? 'text-orange-600 bg-orange-50' : 'text-gray-700 hover:text-orange-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Batches</span>
                    </a>
                    <a href="{{ route('channels.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:bg-orange-50 {{ request()->routeIs('channels.*') ? 'text-orange-600 bg-orange-50' : 'text-gray-700 hover:text-orange-600' }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785"/>
                        </svg>
                        <span>Channels</span>
                    </a>
                    <a href="{{ route('ads.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:bg-orange-50 {{ request()->routeIs('ads.*') ? 'text-orange-600 bg-orange-50' : 'text-gray-700 hover:text-orange-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span>Share & Earn</span>
                    </a>
                    <a href="{{ route('credits.purchase') }}" class="flex items-center space-x-2 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:bg-orange-50 {{ request()->routeIs('credits.*') ? 'text-orange-600 bg-orange-50' : 'text-gray-700 hover:text-orange-600' }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                        <span>Credits</span>
                    </a>
                </nav>

                <!-- Right Side Icons -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- Quick Credit Balance (Desktop) -->
                    <div class="hidden lg:flex items-center space-x-2 bg-gradient-to-r from-orange-50 to-purple-50 px-3 py-1.5 rounded-lg border border-orange-200">
                        <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                        <span class="text-sm  text-gray-700">{{ number_format(auth()->user()->credits ?? 0) }}</span>
                    </div>

                    <!-- Notification Bell -->
                    @livewire('notification-bell')

                    <!-- User Menu Dropdown -->
                    <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button type="button" 
                                class="flex items-center space-x-2 p-1.5 sm:p-2 rounded-xl hover:bg-gray-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                                @click="open = !open"
                                aria-haspopup="true"
                                :aria-expanded="open.toString()">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-r from-orange-400 to-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-white text-xs sm:text-sm ">
                                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                </span>
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700 max-w-24 truncate">
                                {{ auth()->user()->name ?? 'User' }}
                            </span>
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500 transition-transform duration-200" 
                                 :class="{ 'rotate-180': open }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Backdrop -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 z-40 "
                             @click="open = false"
                             aria-hidden="true"
                             style="display: none;">
                        </div>
                        
                        <!-- Dropdown Menu (Unified for Mobile & Desktop) -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-4 lg:right-0 mt-2 w-72 origin-top-right bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 max-h-[calc(100vh-4.5rem)] overflow-y-auto"
                             @click.away="open = false"
                             style="display: none;">
                            
                            <!-- User Info -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-orange-400 to-purple-500 rounded-xl flex items-center justify-center">
                                        <span class="text-white ">
                                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm  text-gray-900 truncate">{{ auth()->user()->name ?? 'User' }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'Email not set' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Wallet Info -->
                            <div class="px-4 py-3 bg-gradient-to-r from-orange-50 to-purple-50">
                                <div class="grid grid-cols-2 gap-3 text-center">
                                    <div>
                                        <p class="text-xs text-gray-600">Credits</p>
                                        <p class="text-sm  text-orange-600">{{ number_format(auth()->user()->credits ?? 0) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Balance</p>
                                        <p class="text-sm  text-purple-600">₦{{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Mobile-Only Main Navigation -->
                            <div class="py-2 border-b border-gray-100 lg:hidden">
                                <a href="{{ route('home') }}" class="flex items-center px-4 py-2 text-sm  transition-colors {{ request()->routeIs('home') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600' }}">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    Batches
                                </a>
                                <a href="{{ route('channels.index') }}" class="flex items-center px-4 py-2 text-sm  transition-colors {{ request()->routeIs('channels.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600' }}">
                                    <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785"/></svg>
                                    Channels
                                </a>
                                <a href="{{ route('channel-bookings.index') }}" class="flex items-center px-4 py-2 text-sm  transition-colors {{ request()->routeIs('channel-bookings.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600' }}">
                                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-3">
  <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" />
</svg>

                                    Channel Bookings
                                </a>
                                <a href="{{ route('ads.index') }}" class="flex items-center px-4 py-2 text-sm  transition-colors {{ request()->routeIs('ads.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600' }}">
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-3">
  <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
</svg>

                                    Share & Earn
                                </a>
                                <a href="{{ route('my-batches') }}" class="flex items-center px-4 py-2 text-sm  transition-colors {{ request()->routeIs('my-batches') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600' }}">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    My Batches
                                </a>
                                <a href="{{ route('credits.purchase') }}" class="flex items-center px-4 py-2 text-sm  transition-colors {{ request()->routeIs('credits.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600' }}">
                                    <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path></svg>
                                    Credits
                                </a>
                                <a href="{{ route('referrals') }}" class="flex items-center px-4 py-2 text-sm  transition-colors {{ request()->routeIs('referrals') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600' }}">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Referrals
                                </a>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-2">
                                <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm  text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Profile Settings
                                </a>
                                <a href="{{ route('transactions.index') }}" class="flex items-center px-4 py-2 text-sm  text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Transactions
                                </a>

                            </div>

                            <!-- Logout -->
                            <div class="border-t border-gray-100 pt-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm  text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <style>
    /* Backdrop blur support */
    .backdrop-blur-md {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    
    /* Smooth transitions for AlpineJS */
    [x-cloak] {
        display: none !important;
    }

    /* Custom scrollbar for dropdown menu */
    .overflow-y-auto::-webkit-scrollbar {
        width: 5px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #f97316, #a855f7);
        border-radius: 5px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #ea580c, #9333ea);
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add ripple effect to buttons
        const buttons = document.querySelectorAll('button, a[class*="bg-gradient"]');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (this.disabled) return;
                
                // Ensure ripple is contained
                this.style.position = 'relative';
                this.style.overflow = 'hidden';

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
                    animation: ripple 600ms linear;
                    pointer-events: none;
                `;
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    });
    
    // Inject CSS animation for ripple effect
    if (!document.getElementById('ripple-animation-style')) {
        const style = document.createElement('style');
        style.id = 'ripple-animation-style';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    </script>
</div>