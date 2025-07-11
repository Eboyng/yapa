<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">

        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-2 flex items-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7 mr-2 text-orange-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
                Batch Management
            </h1>
            <p class="text-gray-600 text-sm sm:text-base">Manage your batches and share them to earn rewards</p>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6 sm:mb-8" x-data="{ activeTab: 'batches' }">
            <div
                class="bg-white/80 backdrop-blur-sm p-1 rounded-xl shadow-sm border border-gray-200/80 inline-flex space-x-1 w-full sm:w-auto overflow-x-auto">
                <button @click="activeTab = 'batches'"
                    :class="activeTab === 'batches' ? 'bg-white shadow-sm text-orange-600' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex-1 sm:flex-none py-2.5 px-4 sm:px-6 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none whitespace-nowrap flex items-center justify-center">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    My Batches
                </button>
                <button @click="activeTab = 'sharing'"
                    :class="activeTab === 'sharing' ? 'bg-white shadow-sm text-orange-600' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex-1 sm:flex-none py-2.5 px-4 sm:px-6 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none whitespace-nowrap flex items-center justify-center">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                        </path>
                    </svg>
                    Share Batches
                </button>
            </div>

            <!-- My Batches Tab Content -->
            <div x-show="activeTab === 'batches'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                <!-- Filter Tabs for Batches -->
                <div class="mt-6 mb-6">
                    <div
                        class="bg-white/80 backdrop-blur-sm p-1 rounded-xl shadow-sm border border-gray-200/80 inline-flex space-x-1">
                        <button wire:click="$set('filter', 'all')"
                            class="py-2 px-3 sm:px-4 rounded-lg font-medium text-xs sm:text-sm transition-all duration-200 focus:outline-none transform hover:scale-105
                                   {{ $filter === 'all' ? 'bg-white shadow text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                            All Batches
                        </button>
                        <button wire:click="$set('filter', 'active')"
                            class="py-2 px-3 sm:px-4 rounded-lg font-medium text-xs sm:text-sm transition-all duration-200 focus:outline-none transform hover:scale-105
                                   {{ $filter === 'active' ? 'bg-white shadow text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                            Active
                        </button>
                        <button wire:click="$set('filter', 'closed')"
                            class="py-2 px-3 sm:px-4 rounded-lg font-medium text-xs sm:text-sm transition-all duration-200 focus:outline-none transform hover:scale-105
                                   {{ $filter === 'closed' ? 'bg-white shadow text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                            Closed
                        </button>
                    </div>
                </div>

                <!-- Loading Skeleton for Batches -->
                <div wire:loading wire:target="filter"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                    @for ($i = 0; $i < 8; $i++)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-4 sm:p-5 animate-pulse">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                                    <div class="h-5 bg-gray-200 rounded w-16"></div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-4"></div>
                                <div class="space-y-2">
                                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                                    <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                </div>
                            </div>
                            <div class="px-4 sm:px-5 py-3 bg-gray-50 border-t border-gray-100">
                                <div class="h-8 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    @endfor
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 sm:mb-6">
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 animate-fade-in">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500 mr-2 sm:mr-3 mt-0.5 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="flex-1">
                                    @foreach ($errors->all() as $error)
                                        <p class="text-xs sm:text-sm font-medium text-red-800">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Batches Grid -->
                <div wire:loading.remove wire:target="filter">
                    @if ($batches->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                            @foreach ($batches as $batch)
                                <div
                                    class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full group">
                                    <!-- Batch Header & Details -->
                                    <div class="p-4 sm:p-5 flex-1">
                                        <div class="flex items-start justify-between mb-3">
                                            <h3
                                                class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 truncate pr-2 group-hover:text-orange-600 transition-colors">
                                                {{ $batch->name ?? "Batch #{$batch->id}" }}
                                            </h3>
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full flex-shrink-0
                                                {{ $batch->status === 'open' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $batch->status === 'full' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $batch->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $batch->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($batch->status) }}
                                            </span>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="w-full bg-gray-100 rounded-full h-2 mb-4 overflow-hidden">
                                            <div class="bg-gradient-to-r from-orange-500 to-purple-500 h-2 rounded-full transition-all duration-500 ease-out transform group-hover:scale-x-105"
                                                style="width: {{ ($batch->members->count() / $batch->limit) * 100 }}%">
                                            </div>
                                        </div>

                                        <!-- Batch Info -->
                                        <div class="space-y-2 sm:space-y-3">
                                            <div class="flex items-center text-xs sm:text-sm text-gray-600">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full font-medium text-xs {{ $batch->type === 'trial' ? 'bg-green-50 text-green-700' : 'bg-orange-50 text-orange-700' }}">
                                                    {{ $batch->type === 'trial' ? 'Trial' : 'Regular' }}
                                                </span>
                                                @if ($batch->location)
                                                    <span class="mx-2 text-gray-300">•</span>
                                                    <span class="flex items-center truncate">
                                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400 flex-shrink-0"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                        </svg>
                                                        {{ $batch->location }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($batch->interests->count() > 0)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($batch->interests->take(3) as $interest)
                                                        <span
                                                            class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 hover:bg-orange-100 hover:text-orange-700 transition-colors">
                                                            {{ $interest->name }}
                                                        </span>
                                                    @endforeach
                                                    @if ($batch->interests->count() > 3)
                                                        <span
                                                            class="text-xs text-gray-500">+{{ $batch->interests->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Members count -->
                                            <div class="flex items-center text-xs sm:text-sm text-gray-600">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                                {{ $batch->members->count() }}/{{ $batch->limit }} members
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="px-4 sm:px-5 py-3 bg-gray-50 border-t border-gray-100 mt-auto">
                                        @if ($batch->isFull())
                                            <button wire:click="downloadContacts({{ $batch->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="downloadContacts({{ $batch->id }})"
                                                class="w-full inline-flex justify-center items-center px-3 py-2 text-xs sm:text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 disabled:opacity-75 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                <span wire:loading.remove
                                                    wire:target="downloadContacts({{ $batch->id }})">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                    Download Contacts
                                                </span>
                                                <span wire:loading
                                                    wire:target="downloadContacts({{ $batch->id }})">
                                                    <svg class="animate-spin h-4 w-4 text-white" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12"
                                                            r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </button>
                                        @else
                                            <div class="text-center text-xs sm:text-sm font-medium text-gray-500">
                                                @if ($batch->status === 'open')
                                                    Waiting for {{ $batch->limit - $batch->members->count() }} more
                                                    members...
                                                @elseif($batch->status === 'expired')
                                                    Batch has expired
                                                @else
                                                    Batch closed
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 sm:mt-8">
                            {{ $batches->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12 sm:py-16">
                            <div
                                class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 sm:mb-6 bg-gradient-to-r from-orange-100 to-purple-100 rounded-2xl flex items-center justify-center">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">No batches found</h3>
                            <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base max-w-md mx-auto">
                                @if ($filter === 'all')
                                    You haven't joined any batches yet. Find a new batch to join!
                                @elseif($filter === 'active')
                                    You don't have any active batches waiting to be filled.
                                @else
                                    You don't have any closed or completed batches.
                                @endif
                            </p>
                            @if ($filter !== 'all')
                                <div class="mt-6">
                                    <button wire:click="$set('filter', 'all')"
                                        class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                        View All Batches
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Share Batches Tab Content -->
            <div x-show="activeTab === 'sharing'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                <!-- Batch Sharing Section -->
                <div class="mt-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6 sm:mb-8">

                        <!-- Share New Batch Section -->
                        <div class="mb-6 sm:mb-8">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-purple-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Select Batch to Share
                            </h3>

                            @if ($isLoadingBatches)
                                <!-- Loading Skeleton for Batches -->
                                <div class="space-y-3">
                                    @for ($i = 0; $i < 3; $i++)
                                        <div class="bg-gray-50 rounded-xl p-4 animate-pulse">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                                                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                                                    <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                                                </div>
                                                <div class="h-8 bg-gray-200 rounded w-24"></div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            @else
                                <div class="space-y-3">
                                    @forelse($openBatches as $batch)
                                        <div
                                            class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200 hover:border-orange-300 transition-all duration-200 hover:shadow-md">
                                            <div
                                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                                                <div class="flex-1">
                                                    <h4 class="font-medium text-gray-900 text-sm sm:text-base">
                                                        {{ $batch['name'] ?? "Batch #{$batch['id']}" }}</h4>
                                                    <p class="text-xs sm:text-sm text-gray-600 mt-1">
                                                        {{ $batch['description'] ?? 'No description available' }}</p>
                                                    <div
                                                        class="flex items-center space-x-3 sm:space-x-4 mt-2 text-xs text-gray-500">
                                                        <span class="flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                                </path>
                                                            </svg>
                                                            {{ $batch['members_count'] ?? 0 }}/{{ $batch['limit'] ?? 0 }}
                                                            members
                                                        </span>
                                                        @if (isset($batch['location']))
                                                            <span class="flex items-center">
                                                                <svg class="w-3 h-3 mr-1" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                                    </path>
                                                                </svg>
                                                                {{ $batch['location'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <button wire:click="generateBatchShareUrl({{ $batch['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="generateBatchShareUrl({{ $batch['id'] }})"
                                                    class="px-4 py-2 bg-gradient-to-r from-orange-500 to-purple-500 text-white rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 text-xs sm:text-sm font-medium disabled:opacity-50 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                    <span wire:loading.remove
                                                        wire:target="generateBatchShareUrl({{ $batch['id'] }})">
                                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                                            </path>
                                                        </svg>
                                                        Generate Link
                                                    </span>
                                                    <span wire:loading
                                                        wire:target="generateBatchShareUrl({{ $batch['id'] }})">
                                                        <svg class="animate-spin h-4 w-4 inline" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12"
                                                                r="10" stroke="currentColor" stroke-width="4">
                                                            </circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                        </svg>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-8 sm:py-12">
                                            <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300 mx-auto mb-3 sm:mb-4"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                </path>
                                            </svg>
                                            <p class="text-gray-500 text-sm sm:text-base">No batches available for
                                                sharing</p>
                                        </div>
                                    @endforelse
                                </div>
                            @endif
                        </div>

                        <!-- Generated Share URL Section -->
                        @if ($selectedBatch && $shareUrl)
                            <div
                                class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 sm:p-6 mb-6 sm:mb-8">
                                <h3 class="text-lg sm:text-xl font-semibold text-blue-900 mb-4 flex items-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                                        </path>
                                    </svg>
                                    Share: {{ $selectedBatch->name ?? "Batch #{$selectedBatch->id}" }}
                                </h3>

                                <div class="bg-white rounded-xl p-3 sm:p-4 mb-4">
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                        <input type="text" value="{{ $shareUrl }}"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm"
                                            readonly>
                                        <button onclick="copyToClipboard('{{ $shareUrl }}')"
                                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 text-xs sm:text-sm font-medium transform hover:scale-105">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            Copy
                                        </button>
                                    </div>
                                </div>

                                <!-- Share Buttons -->
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                                    <button wire:click="shareBatch('whatsapp')"
                                        class="flex items-center justify-center space-x-1.5 sm:space-x-2 px-3 sm:px-4 py-2 sm:py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-200 font-medium text-xs sm:text-sm transform hover:scale-105">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785" />
                                        </svg>
                                        <span class="hidden sm:inline">WhatsApp</span>
                                    </button>

                                    <button wire:click="shareBatch('facebook')"
                                        class="flex items-center justify-center space-x-1.5 sm:space-x-2 px-3 sm:px-4 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium text-xs sm:text-sm transform hover:scale-105">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                        </svg>
                                        <span class="hidden sm:inline">Facebook</span>
                                    </button>

                                    <button wire:click="shareBatch('twitter')"
                                        class="flex items-center justify-center space-x-1.5 sm:space-x-2 px-3 sm:px-4 py-2 sm:py-3 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-all duration-200 font-medium text-xs sm:text-sm transform hover:scale-105">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                        </svg>
                                        <span class="hidden sm:inline">X (Twitter)</span>
                                    </button>

                                    <button wire:click="shareBatch('copy_link')"
                                        class="flex items-center justify-center space-x-1.5 sm:space-x-2 px-3 sm:px-4 py-2 sm:py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 font-medium text-xs sm:text-sm transform hover:scale-105">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span class="hidden sm:inline">Copy Link</span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- My Shared Batches -->
                        <div>
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                                    </path>
                                </svg>
                                My Shared Batches
                            </h3>

                            @if ($isLoadingBatches)
                                <!-- Loading Skeleton for Shared Batches -->
                                <div class="space-y-4 sm:space-y-6">
                                    @for ($i = 0; $i < 3; $i++)
                                        <div
                                            class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 sm:p-6 animate-pulse">
                                            <div
                                                class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                                <div class="flex-1">
                                                    <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div>
                                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                                </div>
                                                <div class="lg:w-80">
                                                    <div class="h-4 bg-gray-200 rounded mb-2"></div>
                                                    <div class="h-2 bg-gray-200 rounded mb-3"></div>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div class="h-12 bg-gray-200 rounded"></div>
                                                        <div class="h-12 bg-gray-200 rounded"></div>
                                                        <div class="h-12 bg-gray-200 rounded"></div>
                                                        <div class="h-12 bg-gray-200 rounded"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            @else
                                <div class="space-y-4 sm:space-y-6">
                                    @forelse($sharedBatches as $batchData)
                                        @php $batch = $batchData['batch']; @endphp
                                        <div
                                            class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 sm:p-6 border border-gray-200 hover:shadow-lg transition-all duration-300">
                                            <div
                                                class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                                <!-- Batch Info -->
                                                <div class="flex-1">
                                                    <div
                                                        class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 mb-2">
                                                        <h4 class="text-base sm:text-lg font-semibold text-gray-900">
                                                            {{ $batch['name'] ?? "Batch #{$batch['id']}" }}</h4>
                                                        @if ($batchData['rewarded'])
                                                            <span
                                                                class="mt-1 sm:mt-0 px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">✅ Rewarded (100 Credits)</span>
                                                        @elseif($batchData['can_claim_reward'])
                                                            <span
                                                                class="mt-1 sm:mt-0 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">🎉 Reward Available!</span>
                                                        @else
                                                            <span
                                                                class="mt-1 sm:mt-0 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">In
                                                                Progress</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-gray-600 text-xs sm:text-sm mb-3">
                                                        {{ $batch['description'] ?? 'No description available' }}</p>
                                                </div>

                                                <!-- Progress Section -->
                                                <div class="lg:w-80">
                                                    <div class="mb-3">
                                                        <div class="flex justify-between text-xs sm:text-sm mb-1">
                                                            <span class="text-gray-600">Progress to Reward (100 Credits)</span>
                                                            <span
                                                                class="font-medium text-gray-900">{{ $batchData['share_count'] }}/10 joined</span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-3 relative overflow-hidden">
                                                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-500 ease-out"
                                                                style="width: {{ $batchData['progress_percentage'] }}%">
                                                            </div>
                                                            @if($batchData['progress_percentage'] >= 100)
                                                                <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full animate-pulse"></div>
                                                            @endif
                                                        </div>
                                                        @if($batchData['share_count'] >= 10 && $batchData['rewarded'])
                                                            <div class="mt-2 text-center">
                                                                <span class="text-green-600 font-medium text-xs">🎉 You earned 100 credits for this batch!</span>
                                                            </div>
                                                        @elseif($batchData['share_count'] >= 10 && !$batchData['rewarded'])
                                                            <div class="mt-2 text-center">
                                                                <span class="text-yellow-600 font-medium text-xs">🎁 Reward processing...</span>
                                                            </div>
                                                        @else
                                                            <div class="mt-2 text-center">
                                                                <span class="text-gray-500 text-xs">{{ 10 - $batchData['share_count'] }} more needed for reward</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Platform Stats -->
                                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                                        <div
                                                            class="bg-white rounded-lg p-2 text-center hover:bg-green-50 transition-colors">
                                                            <div class="font-medium text-green-600">
                                                                {{ $batchData['platform_stats']['whatsapp'] }}</div>
                                                            <div class="text-gray-500">WhatsApp</div>
                                                        </div>
                                                        <div
                                                            class="bg-white rounded-lg p-2 text-center hover:bg-blue-50 transition-colors">
                                                            <div class="font-medium text-blue-600">
                                                                {{ $batchData['platform_stats']['facebook'] }}</div>
                                                            <div class="text-gray-500">Facebook</div>
                                                        </div>
                                                        <div
                                                            class="bg-white rounded-lg p-2 text-center hover:bg-blue-50 transition-colors">
                                                            <div class="font-medium text-blue-400">
                                                                {{ $batchData['platform_stats']['twitter'] }}</div>
                                                            <div class="text-gray-500">Twitter</div>
                                                        </div>
                                                        <div
                                                            class="bg-white rounded-lg p-2 text-center hover:bg-gray-50 transition-colors">
                                                            <div class="font-medium text-gray-600">
                                                                {{ $batchData['platform_stats']['copy_link'] }}</div>
                                                            <div class="text-gray-500">Copy Link</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Summary Stats -->
                                            <div class="mt-4 pt-4 border-t border-gray-200">
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs sm:text-sm">
                                                    <div
                                                        class="text-center bg-white rounded-lg p-3 hover:bg-orange-50 transition-colors">
                                                        <div class="font-semibold text-gray-900">
                                                            {{ $batchData['total_shares'] }}</div>
                                                        <div class="text-gray-500">Total Shares</div>
                                                    </div>
                                                    <div
                                                        class="text-center bg-white rounded-lg p-3 hover:bg-blue-50 transition-colors">
                                                        <div class="font-semibold text-gray-900">
                                            {{ $batchData['share_count'] }}</div>
                                                        <div class="text-gray-500">New Members</div>
                                                    </div>
                                                    <div
                                                        class="text-center bg-white rounded-lg p-3 hover:bg-green-50 transition-colors">
                                                        <div class="font-semibold text-orange-600">
                                            ₦{{ number_format($batchData['share_count'] * 100, 2) }}
                                        </div>
                                                        <div class="text-gray-500">Potential Reward</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-12 sm:py-16">
                                            <div
                                                class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 bg-gradient-to-r from-orange-100 to-purple-100 rounded-2xl flex items-center justify-center">
                                                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <h4 class="text-lg font-semibold text-gray-900 mb-2">No Shared Batches</h4>
                                            <p class="text-gray-500 text-sm sm:text-base">You haven't shared any
                                                batches yet. Start sharing to earn rewards!</p>
                                        </div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
            animation: fade-in 0.3s ease-out forwards;
        }

        /* Custom notification styles */
        .notification-enter {
            transform: translateX(100%);
            opacity: 0;
        }

        .notification-enter-active {
            transform: translateX(0);
            opacity: 1;
            transition: all 0.3s ease-out;
        }

        .notification-exit {
            transform: translateX(0);
            opacity: 1;
        }

        .notification-exit-active {
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease-in;
        }
    </style>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Create notification with modern styling
                const notification = document.createElement('div');
                notification.className =
                    'fixed top-4 right-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-4 py-3 rounded-xl shadow-lg z-50 flex items-center space-x-2 notification-enter transform transition-all duration-300';

                notification.innerHTML = `
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-sm font-medium">Link copied to clipboard!</span>
            `;

                document.body.appendChild(notification);

                // Trigger enter animation
                requestAnimationFrame(() => {
                    notification.classList.remove('notification-enter');
                    notification.classList.add('notification-enter-active');
                });

                // Auto remove with exit animation
                setTimeout(() => {
                    notification.classList.remove('notification-enter-active');
                    notification.classList.add('notification-exit-active');

                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 2700);
            }).catch(function() {
                // Fallback notification for copy failure
                const notification = document.createElement('div');
                notification.className =
                    'fixed top-4 right-4 bg-gradient-to-r from-red-500 to-pink-500 text-white px-4 py-3 rounded-xl shadow-lg z-50 flex items-center space-x-2';
                notification.innerHTML = `
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span class="text-sm font-medium">Failed to copy link</span>
            `;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            });
        }

        // Add micro-interactions for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Add click effect to buttons
            document.addEventListener('click', function(e) {
                if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                    const button = e.target.tagName === 'BUTTON' ? e.target : e.target.closest('button');
                    button.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        button.style.transform = '';
                    }, 100);
                }
            });

            // Add hover effects to cards
            const cards = document.querySelectorAll('.hover\\:shadow-xl');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</div>
