<!-- Mobile Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-gray-200/50 z-50 md:hidden shadow-lg">
    <div class="flex justify-around items-center px-2 py-1.5 max-w-md mx-auto">
        
        <!-- Batches -->
        <a href="{{ route('home') }}" 
           class="nav-item {{ request()->routeIs('home') ? 'active' : '' }} flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 min-w-0">
            <div class="relative">
                <!-- Batches Icon (Grid/Collection) -->
                <svg class="w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <div class="indicator absolute -top-1 -right-1 w-1.5 h-1.5 bg-blue-500 rounded-full {{ request()->routeIs('home') ? 'opacity-100' : 'opacity-0' }}"></div>
            </div>
            <span class="text-[10px] font-medium mt-1 truncate">Batches</span>
        </a>

        <!-- Channels -->
        <a href="{{ route('channels.index') }}" 
           class="nav-item {{ request()->routeIs('channels.*') ? 'active' : '' }} flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 min-w-0">
            <div class="relative">
                <!-- Channels Icon (TV/Broadcast) -->
                <svg class="w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2M7 4h10M7 4v16a1 1 0 001 1h8a1 1 0 001-1V4M5 8h14M5 12h14M5 16h14"/>
                </svg>
                <div class="indicator absolute -top-1 -right-1 w-1.5 h-1.5 bg-blue-500 rounded-full {{ request()->routeIs('channels.*') ? 'opacity-100' : 'opacity-0' }}"></div>
            </div>
            <span class="text-[10px] font-medium mt-1 truncate">Channels</span>
        </a>

        <!-- Earn -->
        <a href="{{ route('ads.index') }}" 
           class="nav-item {{ request()->routeIs('ads.*') ? 'active' : '' }} flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 min-w-0">
            <div class="relative">
                <!-- Earn Icon (Trending Up) -->
                <svg class="w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <div class="indicator absolute -top-1 -right-1 w-1.5 h-1.5 bg-blue-500 rounded-full {{ request()->routeIs('ads.*') ? 'opacity-100' : 'opacity-0' }}"></div>
            </div>
            <span class="text-[10px] font-medium mt-1 truncate">Earn</span>
        </a>

        <!-- Credits -->
        <a href="{{ route('credits.purchase') }}" 
           class="nav-item {{ request()->routeIs('credits.*') ? 'active' : '' }} flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 min-w-0">
            <div class="relative">
                <!-- Credits Icon (Credit Card/Wallet) -->
                <svg class="w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <div class="indicator absolute -top-1 -right-1 w-1.5 h-1.5 bg-blue-500 rounded-full {{ request()->routeIs('credits.*') ? 'opacity-100' : 'opacity-0' }}"></div>
            </div>
            <span class="text-[10px] font-medium mt-1 truncate">Credits</span>
        </a>

        <!-- Profile -->
        <a href="{{ route('profile') }}" 
           class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }} flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 min-w-0">
            <div class="relative">
                <!-- Profile Icon -->
                <svg class="w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <div class="indicator absolute -top-1 -right-1 w-1.5 h-1.5 bg-blue-500 rounded-full {{ request()->routeIs('profile') ? 'opacity-100' : 'opacity-0' }}"></div>
            </div>
            <span class="text-[10px] font-medium mt-1 truncate">Profile</span>
        </a>
    </div>

<style>
    /* Custom styles for the navigation */
    .nav-item {
        color: #6b7280; /* gray-500 */
        flex: 1;
        max-width: 80px;
    }
    
    .nav-item.active {
        color: #3b82f6; /* blue-500 */
        background-color: #eff6ff; /* blue-50 */
    }
    
    .nav-item:hover {
        transform: translateY(-1px);
        color: #4f46e5; /* indigo-600 */
    }
    
    .nav-item.active .indicator {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    /* Ensure proper mobile spacing */
    @media (max-width: 380px) {
        .nav-item {
            padding: 6px 8px;
        }
        .nav-item span {
            font-size: 9px;
        }
        .nav-item svg {
            width: 18px;
            height: 18px;
        }
    }
</style>
</nav>

<!-- Bottom Padding for Mobile Content -->
