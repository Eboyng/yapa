<footer class="">
    <!-- Mobile Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-gray-100 z-50 md:hidden shadow-2xl">
    <div class="flex justify-around items-center px-3 py-2 max-w-md mx-auto">
        
        <!-- Batches -->
        <a href="{{ route('home') }}" 
           class="nav-item {{ request()->routeIs('home') ? 'active' : '' }} flex flex-col items-center py-2.5 px-3 rounded-2xl transition-all duration-300 min-w-0 relative overflow-hidden group">
            <div class="relative z-10">
                <!-- Batches Icon (Grid/Collection) -->
                <svg class="w-5 h-5 transition-all duration-300 group-active:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <div class="indicator absolute -top-1 -right-1 w-2 h-2 rounded-full transition-all duration-300 {{ request()->routeIs('home') ? 'bg-gradient-to-r from-orange-400 to-purple-400 opacity-100 scale-100' : 'opacity-0 scale-0' }}"></div>
            </div>
            <span class="text-[10px] font-medium mt-1 truncate relative z-10 transition-all duration-300">Batches</span>
            <!-- Active background -->
            <div class="absolute inset-0 bg-gradient-to-r from-orange-50 to-purple-50 rounded-2xl opacity-0 transition-all duration-300 {{ request()->routeIs('home') ? 'opacity-100' : 'group-hover:opacity-50' }}"></div>
        </a>

        <!-- Channels -->
        <a href="{{ route('channels.index') }}" 
           class="nav-item {{ request()->routeIs('channels.*') ? 'active' : '' }} flex flex-col items-center py-2.5 px-3 rounded-2xl transition-all duration-300 min-w-0 relative overflow-hidden group">
            <div class="relative z-10">
                <!-- Channels Icon (TV/Broadcast) -->
                <svg class="w-5 h-5 transition-all duration-300 group-active:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2M7 4h10M7 4v16a1 1 0 001 1h8a1 1 0 001-1V4M5 8h14M5 12h14M5 16h14"/>
                </svg>
                <div class="indicator absolute -top-1 -right-1 w-2 h-2 rounded-full transition-all duration-300 {{ request()->routeIs('channels.*') ? 'bg-gradient-to-r from-orange-400 to-purple-400 opacity-100 scale-100' : 'opacity-0 scale-0' }}"></div>
            </div>
            <span class="text-[10px] font-medium mt-1 truncate relative z-10 transition-all duration-300">Channels</span>
            <!-- Active background -->
            <div class="absolute inset-0 bg-gradient-to-r from-orange-50 to-purple-50 rounded-2xl opacity-0 transition-all duration-300 {{ request()->routeIs('channels.*') ? 'opacity-100' : 'group-hover:opacity-50' }}"></div>
        </a>

        <!-- Earn -->
        <a href="{{ route('ads.index') }}" 
           class="nav-item {{ request()->routeIs('ads.*') ? 'active' : '' }} flex flex-col items-center py-2.5 px-3 rounded-2xl transition-all duration-300 min-w-0 relative overflow-hidden group">
            <div class="relative z-10">
                <!-- Earn Icon (Trending Up) -->
                <svg class="w-5 h-5 transition-all duration-300 group-active:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <div class="indicator absolute -top-1 -right-1 w-2 h-2 rounded-full transition-all duration-300 {{ request()->routeIs('ads.*') ? 'bg-gradient-to-r from-orange-400 to-purple-400 opacity-100 scale-100' : 'opacity-0 scale-0' }}"></div>
            </div>
            <span class="text-[10px] font-medium mt-1 truncate relative z-10 transition-all duration-300">Earn</span>
            <!-- Active background -->
            <div class="absolute inset-0 bg-gradient-to-r from-orange-50 to-purple-50 rounded-2xl opacity-0 transition-all duration-300 {{ request()->routeIs('ads.*') ? 'opacity-100' : 'group-hover:opacity-50' }}"></div>
        </a>

        <!-- Credits -->
        <a href="{{ route('credits.purchase') }}" 
           class="nav-item {{ request()->routeIs('credits.*') ? 'active' : '' }} flex flex-col items-center py-2.5 px-3 rounded-2xl transition-all duration-300 min-w-0 relative overflow-hidden group">
            <div class="relative z-10">
                <!-- Credits Icon (Credit Card/Wallet) -->
                <svg class="w-5 h-5 transition-all duration-300 group-active:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <div class="indicator absolute -top-1 -right-1 w-2 h-2 rounded-full transition-all duration-300 {{ request()->routeIs('credits.*') ? 'bg-gradient-to-r from-orange-400 to-purple-400 opacity-100 scale-100' : 'opacity-0 scale-0' }}"></div>
            </div>
            <span class="text-[10px] font-medium mt-1 truncate relative z-10 transition-all duration-300">Credits</span>
            <!-- Active background -->
            <div class="absolute inset-0 bg-gradient-to-r from-orange-50 to-purple-50 rounded-2xl opacity-0 transition-all duration-300 {{ request()->routeIs('credits.*') ? 'opacity-100' : 'group-hover:opacity-50' }}"></div>
        </a>

        <!-- Profile -->
        <a href="{{ route('profile') }}" 
           class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }} flex flex-col items-center py-2.5 px-3 rounded-2xl transition-all duration-300 min-w-0 relative overflow-hidden group">
            <div class="relative z-10">
                <!-- Profile Icon -->
                <svg class="w-5 h-5 transition-all duration-300 group-active:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <div class="indicator absolute -top-1 -right-1 w-2 h-2 rounded-full transition-all duration-300 {{ request()->routeIs('profile') ? 'bg-gradient-to-r from-orange-400 to-purple-400 opacity-100 scale-100' : 'opacity-0 scale-0' }}"></div>
            </div>
            <span class="text-[10px] font-medium mt-1 truncate relative z-10 transition-all duration-300">Profile</span>
            <!-- Active background -->
            <div class="absolute inset-0 bg-gradient-to-r from-orange-50 to-purple-50 rounded-2xl opacity-0 transition-all duration-300 {{ request()->routeIs('profile') ? 'opacity-100' : 'group-hover:opacity-50' }}"></div>
        </a>
    </div>
</nav>

<style>
    /* Custom styles for the navigation */
    .nav-item {
        color: #6b7280; /* gray-500 */
        flex: 1;
        max-width: 70px;
        transform-style: preserve-3d;
    }
    
    .nav-item.active {
        background: linear-gradient(135deg, #fed7aa 0%, #fde68a 50%, #e9d5ff 100%);
        background-size: 200% 200%;
        animation: gradient-shift 3s ease infinite;
        border: 1px solid rgba(251, 146, 60, 0.2);
        box-shadow: 0 4px 20px rgba(251, 146, 60, 0.15);
    }
    
    .nav-item.active,
    .nav-item.active svg,
    .nav-item.active span {
        background: linear-gradient(135deg, #f97316, #a855f7);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 600;
    }
    
    .nav-item:not(.active):hover {
        transform: translateY(-2px);
        color: #f97316; /* orange-500 */
    }
    
    .nav-item.active .indicator {
        animation: pulse-glow 2s infinite;
    }
    
    .nav-item:active {
        transform: translateY(0) scale(0.95);
    }
    
    /* Gradient animation for active state background */
    @keyframes gradient-shift {
        0%, 100% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
    }
    
    /* Enhanced pulse animation with glow */
    @keyframes pulse-glow {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
            box-shadow: 0 0 8px rgba(251, 146, 60, 0.6);
        }
        50% {
            opacity: 0.7;
            transform: scale(1.1);
            box-shadow: 0 0 12px rgba(168, 85, 247, 0.8);
        }
    }
    
    /* Icon scaling on tap */
    .nav-item:active svg {
        transform: scale(0.9);
    }
    
    /* Ensure proper mobile spacing */
    @media (max-width: 380px) {
        .nav-item {
            padding: 8px 6px;
            max-width: 60px;
        }
        .nav-item span {
            font-size: 9px;
        }
        .nav-item svg {
            width: 18px;
            height: 18px;
        }
    }
    
    /* Larger phones */
    @media (min-width: 400px) {
        .nav-item {
            max-width: 80px;
        }
    }
    
    /* Add a subtle blur backdrop effect */
    nav::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
        backdrop-filter: blur(12px);
        z-index: -1;
    }
    
    /* Enhanced shadow */
    nav {
        box-shadow: 
            0 -10px 25px -5px rgba(0, 0, 0, 0.1),
            0 -4px 6px -2px rgba(0, 0, 0, 0.05),
            0 0 0 1px rgba(251, 146, 60, 0.1);
    }
</style>


</footer>