<!-- Profile Header Component -->
<div class="bg-white rounded-2xl p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8 border border-gray-100">
    <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-6">
        <!-- Avatar -->
        <div class="relative">
            <div class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32 rounded-2xl bg-gradient-to-r from-orange-400 to-purple-500 flex items-center justify-center text-white text-xl sm:text-2xl lg:text-3xl font-bold shadow-lg">
                {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
            </div>
            <div class="absolute -bottom-1 -right-1 sm:-bottom-2 sm:-right-2 w-6 h-6 sm:w-8 sm:h-8 bg-green-500 rounded-full border-2 sm:border-4 border-white flex items-center justify-center">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        
        <!-- User Info -->
        <div class="flex-1 text-center sm:text-left">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-1">{{ $user->email }}</p>
            @if($user->location)
            <div class="flex items-center justify-center sm:justify-start mt-2 text-gray-500">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-sm">{{ $user->location }}, Nigeria</span>
            </div>
            @endif
            <div class="flex items-center justify-center sm:justify-start mt-2 text-xs sm:text-sm text-gray-500">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h6a2 2 0 012 2v4M5 9v10a2 2 0 002 2h10a2 2 0 002-2V9M5 9h14"></path>
                </svg>
                Member since {{ $user->created_at->format('M Y') }}
            </div>
        </div>
    </div>
</div>