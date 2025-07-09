<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $brandingSettings['site_name'] ?? 'Yapa' }} - Under Maintenance</title>
    
    @if($brandingSettings['site_favicon'])
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $brandingSettings['site_favicon']) }}">
    @endif
    
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        orange: {
                            50: '#fff7ed',
                            500: '#f97316',
                            600: '#ea580c',
                        },
                        purple: {
                            50: '#faf5ff',
                            500: '#a855f7',
                            600: '#9333ea',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(249, 115, 22, 0.3); }
            50% { box-shadow: 0 0 40px rgba(249, 115, 22, 0.6); }
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes bounce-in {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .float-animation { animation: float 6s ease-in-out infinite; }
        .pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
        .rotate-animation { animation: rotate 20s linear infinite; }
        .bounce-in { animation: bounce-in 0.8s ease-out; }
        
        .gradient-bg {
            background: linear-gradient(-45deg, #ee7724, #d8363a, #dd3675, #b44593);
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
        }
        
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl mx-auto text-center">
        <!-- Floating Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white/10 rounded-full float-animation" style="animation-delay: 0s;"></div>
            <div class="absolute top-3/4 right-1/4 w-24 h-24 bg-white/10 rounded-full float-animation" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-3/4 w-20 h-20 bg-white/10 rounded-full float-animation" style="animation-delay: 4s;"></div>
        </div>
        
        <!-- Main Content -->
        <div class="relative z-10 bg-white/95 backdrop-blur-lg rounded-3xl p-8 md:p-12 shadow-2xl bounce-in">
            <!-- Logo/Icon -->
            <div class="mb-8">
                @if($brandingSettings['site_logo'])
                    <img src="{{ asset('storage/' . $brandingSettings['site_logo']) }}" 
                         alt="{{ $brandingSettings['site_name'] ?? 'Yapa' }}" 
                         class="w-20 h-20 mx-auto pulse-glow rounded-2xl">
                @else
                    <div class="w-20 h-20 mx-auto bg-gradient-to-r from-orange-500 to-purple-500 rounded-2xl flex items-center justify-center pulse-glow">
                        <!-- Maintenance Icon SVG -->
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                @endif
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                We'll Be Back Soon!
            </h1>
            
            <!-- Subtitle -->
            <h2 class="text-xl md:text-2xl font-semibold text-gray-600 mb-6">
                {{ $brandingSettings['site_name'] ?? 'Yapa' }} is Under Maintenance
            </h2>
            
            <!-- Message -->
            <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                {{ $maintenanceSettings['maintenance_message'] ?? 'We are currently performing scheduled maintenance. We will be back shortly.' }}
            </p>
            
            <!-- Countdown Timer -->
            @if($maintenanceSettings['maintenance_end_time'])
                @livewire('maintenance-countdown')
            @endif
            
            <!-- Loading Animation -->
            <div class="mb-8">
                <div class="flex justify-center items-center space-x-2">
                    <div class="w-3 h-3 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0ms;"></div>
                    <div class="w-3 h-3 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 150ms;"></div>
                    <div class="w-3 h-3 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 300ms;"></div>
                </div>
                <p class="text-gray-500 mt-2 text-sm">Working on improvements...</p>
            </div>
            
            <!-- Contact Info -->
            <div class="border-t border-gray-200 pt-6">
                <p class="text-gray-500 text-sm mb-2">Need immediate assistance?</p>
                <div class="flex justify-center space-x-6">
                    <a href="mailto:support@{{ request()->getHost() }}" 
                       class="flex items-center space-x-2 text-orange-600 hover:text-orange-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>Email Support</span>
                    </a>
                    <button onclick="location.reload()" 
                            class="flex items-center space-x-2 text-purple-600 hover:text-purple-700 transition-colors">
                        <svg class="w-4 h-4 rotate-animation" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Refresh Page</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    @if($maintenanceSettings['maintenance_end_time'])
    <script>
        // Countdown Timer
        const endTime = new Date('{{ $maintenanceSettings['maintenance_end_time'] }}').getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            if (distance < 0) {
                // Maintenance period ended, reload page
                location.reload();
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('days').textContent = days.toString().padStart(2, '0');
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        }
        
        // Update countdown every second
        updateCountdown();
        setInterval(updateCountdown, 1000);
        
        // Auto-refresh every 30 seconds to check if maintenance is over
        setInterval(() => {
            fetch(window.location.href, { method: 'HEAD' })
                .then(response => {
                    if (response.status !== 503) {
                        location.reload();
                    }
                })
                .catch(() => {});
        }, 30000);
    </script>
    @endif
    
    @livewireScripts
</body>
</html>