<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Channel Bookings</h1>
                <p class="text-gray-600">Manage your advertisement bookings and track performance</p>
            </div>

            @if (auth()->user()->channels->count() === 0)
                <div class="mt-4 sm:mt-0">
                    <button wire:click="openCreateModal"
                        class="inline-flex items-center px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span class="hidden sm:inline">Create Channel</span>
                        <span class="sm:hidden">Create</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-all duration-200 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Bookings</p>
                        <p
                            class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">
                            {{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-all duration-200 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Pending</p>
                        <p
                            class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">
                            {{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-all duration-200 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Running</p>
                        <p
                            class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">
                            {{ $stats['running'] }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-all duration-200 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Completed</p>
                        <p
                            class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">
                            {{ $stats['completed'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="flex flex-wrap gap-2 sm:gap-3">
                <button wire:click="$set('filter', 'all')"
                    class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-200 transform hover:scale-105 {{ $filter === 'all' ? 'bg-gradient-to-r from-orange-500 to-purple-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    All Bookings
                </button>
                <button wire:click="$set('filter', 'pending')"
                    class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-200 transform hover:scale-105 {{ $filter === 'pending' ? 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Pending ({{ $stats['pending'] }})
                </button>
                <button wire:click="$set('filter', 'accepted')"
                    class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-200 transform hover:scale-105 {{ $filter === 'accepted' ? 'bg-gradient-to-r from-green-400 to-green-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Accepted
                </button>
                <button wire:click="$set('filter', 'running')"
                    class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-200 transform hover:scale-105 {{ $filter === 'running' ? 'bg-gradient-to-r from-blue-400 to-blue-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Running
                </button>
                <button wire:click="$set('filter', 'completed')"
                    class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-200 transform hover:scale-105 {{ $filter === 'completed' ? 'bg-gradient-to-r from-purple-400 to-purple-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Completed
                </button>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @if (auth()->user()->channels->count() === 0)
                <div class="text-center py-16">
                    <div
                        class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-orange-100 to-purple-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No channels found</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        You need to create a channel first before you can view bookings.
                    </p>
                    <div class="mt-6">
                        <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Create Channel
                        </button>
                    </div>
                </div>
            @elseif($bookings->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach ($bookings as $booking)
                        <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 mb-3">
                                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 sm:mb-0">
                                            {{ $booking->title ?: 'Advertisement Booking' }}
                                        </h3>
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium w-fit
                                            @if ($booking->status === 'pending') bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800
                                            @elseif($booking->status === 'accepted') bg-gradient-to-r from-green-100 to-green-200 text-green-800
                                            @elseif($booking->status === 'running') bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800
                                            @elseif($booking->status === 'completed') bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800
                                            @elseif($booking->status === 'rejected') bg-gradient-to-r from-red-100 to-red-200 text-red-800
                                            @else bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 @endif">
                                            @if ($booking->status === 'pending')
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            @elseif($booking->status === 'accepted')
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            @elseif($booking->status === 'running')
                                                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse mr-1"></div>
                                            @elseif($booking->status === 'completed')
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </div>

                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 text-xs sm:text-sm text-gray-600 mb-4">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2M7 4h10l1 16H6L7 4z"></path>
                                            </svg>
                                            <span class="font-medium">Channel:</span>
                                            <span class="ml-1 truncate">{{ $booking->channel->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                            <span class="font-medium">Advertiser:</span>
                                            <span class="ml-1 truncate">{{ $booking->user->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="font-medium">Duration:</span>
                                            <span class="ml-1">{{ $booking->duration_hours }} hours</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                </path>
                                            </svg>
                                            <span class="font-medium">Amount:</span>
                                            <span
                                                class="ml-1 font-semibold text-green-600">₦{{ number_format($booking->total_amount) }}</span>
                                        </div>
                                    </div>

                                    @if ($booking->description)
                                        <p class="text-sm sm:text-base text-gray-700 mb-4 bg-gray-50 p-3 rounded-xl">
                                            {{ $booking->description }}</p>
                                    @endif

                                    @if ($booking->url)
                                        <div class="mb-4 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                                </path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-500">URL:</span>
                                            <a href="{{ $booking->url }}" target="_blank"
                                                class="text-blue-600 hover:text-blue-800 ml-2 truncate transition-colors duration-200 hover:underline">{{ $booking->url }}</a>
                                        </div>
                                    @endif

                                    @if ($booking->images)
                                        <div class="mb-4">
                                            <div class="flex items-center mb-2">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                <span class="text-sm font-medium text-gray-500">Images:</span>
                                            </div>
                                            <div class="flex space-x-2 overflow-x-auto pb-2">
                                                @foreach (json_decode($booking->images) as $image)
                                                    <img src="{{ Storage::url($image) }}" alt="Ad Image"
                                                        class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200 flex-shrink-0">
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="text-xs text-gray-500 bg-gray-50 p-3 rounded-xl">
                                        <div class="flex flex-wrap gap-2">
                                            <span class="flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                Booked: {{ $booking->created_at->format('M d, Y H:i') }}
                                            </span>
                                            @if ($booking->accepted_at)
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    Accepted: {{ $booking->accepted_at->format('M d, Y H:i') }}
                                                </span>
                                            @endif
                                            @if ($booking->started_at)
                                                <span class="flex items-center">
                                                    <div class="w-2 h-2 rounded-full bg-blue-500 mr-1"></div>
                                                    Started: {{ $booking->started_at->format('M d, Y H:i') }}
                                                </span>
                                            @endif
                                            @if ($booking->completed_at)
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1 text-purple-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    Completed: {{ $booking->completed_at->format('M d, Y H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 lg:mt-0 lg:ml-6 flex flex-col sm:flex-row lg:flex-col gap-2">
                                    @if ($booking->status === 'pending')
                                        <button wire:click="acceptBooking({{ $booking->id }})"
                                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Accept
                                        </button>
                                        <button wire:click="rejectBooking({{ $booking->id }})"
                                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-xl text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Reject
                                        </button>
                                    @elseif($booking->status === 'accepted')
                                        <button wire:click="startBooking({{ $booking->id }})"
                                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5-9v1m0 0V4m0 2a5 5 0 105 5">
                                                </path>
                                            </svg>
                                            Start Ad
                                        </button>
                                    @elseif($booking->status === 'running')
                                        <button wire:click="openProofModal({{ $booking->id }})"
                                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            Submit Proof
                                        </button>
                                    @endif

                                    @if ($booking->proof_screenshot && $booking->status === 'proof_submitted')
                                        <div
                                            class="text-xs text-center p-2 bg-gradient-to-r from-yellow-50 to-yellow-100 text-yellow-800 rounded-xl border border-yellow-200">
                                            <div class="flex items-center justify-center mb-1">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="font-medium">Proof submitted</span>
                                            </div>
                                            <p>Waiting for admin approval</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div
                        class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-orange-100 to-purple-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No bookings found</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        @if ($filter === 'all')
                            You don't have any advertisement bookings yet.
                        @else
                            No bookings found with the selected filter.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Channel Creation Modal -->
    @if ($showCreateModal)
        <!-- Mobile Modal (Bottom Sheet) -->
        <div class="fixed inset-0 z-50 overflow-hidden sm:hidden" aria-labelledby="mobile-create-modal-title"
            role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closeCreateModal">
            </div>
            <div
                class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl transform transition-transform duration-300 ease-in-out max-h-[85vh] overflow-hidden">
                <form wire:submit.prevent="createChannel" class="flex flex-col h-full">
                    <!-- Mobile Header -->
                    <div
                        class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50 rounded-t-2xl">
                        <h3 class="text-lg font-semibold text-gray-900" id="mobile-create-modal-title">Create Channel
                        </h3>
                        <button type="button" wire:click="closeCreateModal"
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Content -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        <!-- Channel Name -->
                         <div>
                             <label for="mobile-channel-name"
                                 class="block text-sm font-medium text-gray-700 mb-1">Channel Name *</label>
                             <input type="text" wire:model="name" id="mobile-channel-name"
                                 class="w-full px-3 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm"
                                 placeholder="Enter channel name...">
                             @error('name')
                                 <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                             @enderror
                         </div>

                        <!-- Channel Description -->
                         <div>
                             <label for="mobile-channel-description"
                                 class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                             <textarea wire:model="description" id="mobile-channel-description" rows="3"
                                 class="w-full px-3 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm"
                                 placeholder="Describe your channel..."></textarea>
                             @error('description')
                                 <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                             @enderror
                         </div>

                        <!-- Niche -->
                         <div>
                             <label for="mobile-niche"
                                 class="block text-sm font-medium text-gray-700 mb-1">Niche *</label>
                             <select wire:model="niche" id="mobile-niche"
                                 class="w-full px-3 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                                 <option value="">Select a niche</option>
                                 <option value="technology">Technology</option>
                                 <option value="business">Business</option>
                                 <option value="entertainment">Entertainment</option>
                                 <option value="education">Education</option>
                                 <option value="health">Health & Fitness</option>
                                 <option value="lifestyle">Lifestyle</option>
                                 <option value="news">News</option>
                                 <option value="sports">Sports</option>
                                 <option value="travel">Travel</option>
                                 <option value="food">Food & Cooking</option>
                                 <option value="fashion">Fashion</option>
                                 <option value="finance">Finance</option>
                                 <option value="gaming">Gaming</option>
                                 <option value="music">Music</option>
                                 <option value="art">Art & Design</option>
                                 <option value="science">Science</option>
                                 <option value="politics">Politics</option>
                                 <option value="religion">Religion</option>
                                 <option value="comedy">Comedy</option>
                                 <option value="other">Other</option>
                             </select>
                             @error('niche')
                                 <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                             @enderror
                         </div>

                        <!-- Follower Count -->
                         <div>
                             <label for="mobile-follower-count"
                                 class="block text-sm font-medium text-gray-700 mb-1">Follower Count *</label>
                             <input type="number" wire:model="follower_count" id="mobile-follower-count"
                                 class="w-full px-3 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm"
                                 placeholder="Enter number of followers..." min="1">
                             @error('follower_count')
                                 <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                             @enderror
                         </div>

                        <!-- WhatsApp Link -->
                         <div>
                             <label for="mobile-whatsapp-link"
                                 class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Channel Link *</label>
                             <input type="url" wire:model="whatsapp_link" id="mobile-whatsapp-link"
                                 class="w-full px-3 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm"
                                 placeholder="https://whatsapp.com/channel/...">
                             @error('whatsapp_link')
                                 <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                             @enderror
                         </div>

                        <!-- Sample Screenshot -->
                         <div>
                             <label for="mobile-sample-screenshot"
                                 class="block text-sm font-medium text-gray-700 mb-1">Sample Screenshot *</label>
                             <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                 <div class="space-y-1 text-center">
                                     @if ($sample_screenshot)
                                         <div class="mb-4">
                                             <img src="{{ $sample_screenshot->temporaryUrl() }}" class="mx-auto h-20 w-auto rounded-lg shadow-md">
                                             <button type="button" wire:click="$set('sample_screenshot', null)" class="mt-2 text-sm text-red-600 hover:text-red-800">
                                                 Remove
                                             </button>
                                         </div>
                                     @else
                                         <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                             <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                         </svg>
                                     @endif
                                     <div class="flex text-xs text-gray-600">
                                         <label for="mobile-sample-screenshot" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                             <span>Upload screenshot</span>
                                             <input id="mobile-sample-screenshot" wire:model="sample_screenshot" type="file" accept="image/*" class="sr-only">
                                         </label>
                                     </div>
                                     <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
                                 </div>
                             </div>
                             @error('sample_screenshot')
                                 <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                             @enderror
                         </div>
                    </div>

                    <!-- Mobile Footer -->
                    <div class="p-4 bg-gray-50 border-t border-gray-200 space-y-2">
                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full inline-flex justify-center items-center px-4 py-3 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 transition-all duration-200">
                            <span wire:loading.remove>Create Channel</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Creating...
                            </span>
                        </button>
                        <button type="button" wire:click="closeCreateModal"
                            class="w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Desktop Modal -->
        <div class="hidden sm:block fixed inset-0 z-50 overflow-y-auto" aria-labelledby="desktop-create-modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeCreateModal">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="createChannel">
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="w-full">
                                <h3 class="text-xl leading-6 font-semibold text-gray-900 mb-6"
                                    id="desktop-create-modal-title">
                                    Create New Channel
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Channel Name -->
                                    <div class="md:col-span-2">
                                        <label for="desktop-channel-name"
                                            class="block text-sm font-medium text-gray-700 mb-2">Channel Name
                                            *</label>
                                        <input type="text" wire:model="name" id="desktop-channel-name"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                            placeholder="Enter channel name...">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Niche -->
                                    <div>
                                        <label for="desktop-niche"
                                            class="block text-sm font-medium text-gray-700 mb-2">Niche
                                            *</label>
                                        <select wire:model="niche" id="desktop-niche"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                            <option value="">Select a niche</option>
                                            <option value="technology">Technology</option>
                                            <option value="business">Business</option>
                                            <option value="entertainment">Entertainment</option>
                                            <option value="education">Education</option>
                                            <option value="health">Health & Fitness</option>
                                            <option value="lifestyle">Lifestyle</option>
                                            <option value="news">News</option>
                                            <option value="sports">Sports</option>
                                            <option value="travel">Travel</option>
                                            <option value="food">Food & Cooking</option>
                                            <option value="fashion">Fashion</option>
                                            <option value="finance">Finance</option>
                                            <option value="gaming">Gaming</option>
                                            <option value="music">Music</option>
                                            <option value="art">Art & Design</option>
                                            <option value="science">Science</option>
                                            <option value="politics">Politics</option>
                                            <option value="religion">Religion</option>
                                            <option value="comedy">Comedy</option>
                                            <option value="other">Other</option>
                                        </select>
                                        @error('niche')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Follower Count -->
                                    <div>
                                        <label for="desktop-follower-count"
                                            class="block text-sm font-medium text-gray-700 mb-2">Follower Count
                                            *</label>
                                        <input type="number" wire:model="follower_count"
                                            id="desktop-follower-count"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                            placeholder="Enter number of followers..." min="1">
                                        @error('follower_count')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- WhatsApp Link -->
                                    <div class="md:col-span-2">
                                        <label for="desktop-whatsapp-link"
                                            class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Channel Link
                                            *</label>
                                        <input type="url" wire:model="whatsapp_link" id="desktop-whatsapp-link"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                            placeholder="https://whatsapp.com/channel/...">
                                        @error('whatsapp_link')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="md:col-span-2">
                                        <label for="desktop-description"
                                            class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea wire:model="description" id="desktop-description" rows="4"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                            placeholder="Describe your channel..."></textarea>
                                        @error('description')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Sample Screenshot -->
                                    <div class="md:col-span-2">
                                        <label for="desktop-sample-screenshot"
                                            class="block text-sm font-medium text-gray-700 mb-2">Sample Screenshot *</label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                @if ($sample_screenshot)
                                                    <div class="mb-4">
                                                        <img src="{{ $sample_screenshot->temporaryUrl() }}" class="mx-auto h-32 w-auto rounded-lg shadow-md">
                                                        <button type="button" wire:click="$set('sample_screenshot', null)" class="mt-2 text-sm text-red-600 hover:text-red-800">
                                                            Remove
                                                        </button>
                                                    </div>
                                                @else
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                @endif
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="desktop-sample-screenshot" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                                        <span>Upload a screenshot</span>
                                                        <input id="desktop-sample-screenshot" wire:model="sample_screenshot" type="file" accept="image/*" class="sr-only">
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                            </div>
                                        </div>
                                        @error('sample_screenshot')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 px-6 py-4 flex flex-row-reverse space-x-3 space-x-reverse rounded-b-2xl">
                            <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex justify-center items-center px-6 py-3 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 transition-all duration-200 transform hover:scale-105">
                                <span wire:loading.remove>Create Channel</span>
                                <span wire:loading class="flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Creating...
                                </span>
                            </button>
                            <button type="button" wire:click="closeCreateModal"
                                class="inline-flex justify-center items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Proof Submission Modal -->
    @if ($showProofModal && $selectedBooking)
        <!-- Mobile Modal (Bottom Sheet) -->
        <div class="fixed inset-0 z-50 overflow-hidden sm:hidden" aria-labelledby="mobile-proof-modal-title"
            role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closeProofModal">
            </div>
            <div
                class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl transform transition-transform duration-300 ease-in-out max-h-[85vh] overflow-hidden">
                <form wire:submit.prevent="submitProof" class="flex flex-col h-full">
                    <!-- Mobile Header -->
                    <div
                        class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50 rounded-t-2xl">
                        <h3 class="text-lg font-semibold text-gray-900" id="mobile-proof-modal-title">Submit Proof
                        </h3>
                        <button type="button" wire:click="closeProofModal"
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Content -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        <!-- Proof Screenshot -->
                        <div>
                            <label for="mobile-proof-screenshot"
                                class="block text-sm font-medium text-gray-700 mb-1">Proof Screenshot *</label>
                            <input type="file" wire:model="proof_screenshot" id="mobile-proof-screenshot"
                                accept="image/*"
                                class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            @error('proof_screenshot')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Proof Description -->
                        <div>
                            <label for="mobile-proof-description"
                                class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                            <textarea wire:model="proof_description" id="mobile-proof-description" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm"
                                placeholder="Describe how the advertisement was completed..."></textarea>
                            @error('proof_description')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Mobile Footer -->
                    <div class="p-4 bg-gray-50 border-t border-gray-200 space-y-2">
                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full inline-flex justify-center items-center px-4 py-3 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-50 transition-all duration-200">
                            <span wire:loading.remove>Submit Proof</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                        <button type="button" wire:click="closeProofModal"
                            class="w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Desktop Modal -->
        <div class="hidden sm:block fixed inset-0 z-50 overflow-y-auto" aria-labelledby="desktop-proof-modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeProofModal">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="submitProof">
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="w-full">
                                <h3 class="text-xl leading-6 font-semibold text-gray-900 mb-6"
                                    id="desktop-proof-modal-title">
                                    Submit Proof of Completion
                                </h3>

                                <div class="space-y-6">
                                    <!-- Proof Screenshot -->
                                    <div>
                                        <label for="desktop-proof-screenshot"
                                            class="block text-sm font-medium text-gray-700 mb-2">Proof Screenshot
                                            *</label>
                                        <input type="file" wire:model="proof_screenshot"
                                            id="desktop-proof-screenshot" accept="image/*"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                        @error('proof_screenshot')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Proof Description -->
                                    <div>
                                        <label for="desktop-proof-description"
                                            class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                        <textarea wire:model="proof_description" id="desktop-proof-description" rows="4"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                            placeholder="Describe how the advertisement was completed..."></textarea>
                                        @error('proof_description')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 px-6 py-4 flex flex-row-reverse space-x-3 space-x-reverse rounded-b-2xl">
                            <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex justify-center items-center px-6 py-3 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-50 transition-all duration-200 transform hover:scale-105">
                                <span wire:loading.remove>Submit Proof</span>
                                <span wire:loading class="flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Submitting...
                                </span>
                            </button>
                            <button type="button" wire:click="closeProofModal"
                                class="inline-flex justify-center items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        /* Custom scrollbar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #f97316, #a855f7);
            border-radius: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #ea580c, #9333ea);
        }

        /* Pulse animation for running status */
        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse {
            animation: pulse-dot 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>

</div>
