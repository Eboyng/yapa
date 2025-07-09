<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
   
        <!-- Filter Tabs -->
        <div class="mb-8">
            <div class="bg-white/80 backdrop-blur-sm p-1.5 rounded-xl shadow-sm border border-gray-200/80 inline-flex space-x-1">
                <button 
                    wire:click="$set('filter', 'all')"
                    class="py-2 px-4 rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none
                           {{ $filter === 'all' ? 'bg-white shadow text-orange-600' : 'text-gray-600 hover:bg-gray-500/5' }}">
                    All Batches
                </button>
                <button 
                    wire:click="$set('filter', 'active')"
                    class="py-2 px-4 rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none
                           {{ $filter === 'active' ? 'bg-white shadow text-orange-600' : 'text-gray-600 hover:bg-gray-500/5' }}">
                    Active
                </button>
                <button 
                    wire:click="$set('filter', 'closed')"
                    class="py-2 px-4 rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none
                           {{ $filter === 'closed' ? 'bg-white shadow text-orange-600' : 'text-gray-600 hover:bg-gray-500/5' }}">
                    Closed
                </button>
            </nav>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 animate-fade-in">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Batches Grid -->
    @if($batches->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($batches as $batch)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">
                    <!-- Batch Header & Details -->
                    <div class="p-4 sm:p-5 flex-1">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate pr-2">
                                {{ $batch->name ?? "Batch #{$batch->id}" }}
                            </h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full flex-shrink-0
                                {{ $batch->status === 'open' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $batch->status === 'full' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $batch->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $batch->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($batch->status) }}
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
                            <div class="bg-gradient-to-r from-orange-500 to-purple-500 h-2 rounded-full transition-all duration-500 ease-out" 
                                 style="width: {{ $batch->members->count() / $batch->limit * 100 }}%"></div>
                        </div>

                        <!-- Batch Info -->
                        <div class="space-y-3">
                            <div class="flex items-center text-xs sm:text-sm text-gray-600">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-medium {{ $batch->type === 'trial' ? 'bg-green-50 text-green-700' : 'bg-orange-50 text-orange-700' }}">
                                    {{ $batch->type === 'trial' ? 'Trial' : 'Regular' }}
                                </span>
                                @if($batch->location)
                                    <span class="mx-2 text-gray-300">•</span>
                                    <span class="flex items-center truncate">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $batch->location }}
                                    </span>
                                @endif
                            </div>

                            @if($batch->interests->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($batch->interests->take(3) as $interest)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $interest->name }}
                                        </span>
                                    @endforeach
                                    @if($batch->interests->count() > 3)
                                        <span class="text-xs text-gray-500">+{{ $batch->interests->count() - 3 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-4 sm:px-5 py-3 bg-gray-50 border-t border-gray-100 mt-auto">
                        @if($batch->isFull())
                            <button 
                                wire:click="downloadContacts({{ $batch->id }})"
                                wire:loading.attr="disabled"
                                wire:target="downloadContacts({{ $batch->id }})"
                                class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 disabled:opacity-75 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                                <span wire:loading.remove wire:target="downloadContacts({{ $batch->id }})">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download Contacts
                                </span>
                                <span wire:loading wire:target="downloadContacts({{ $batch->id }})">
                                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                            </button>
                        @else
                            <div class="text-center text-sm font-medium text-gray-500">
                                @if($batch->status === 'open')
                                    Waiting for {{ $batch->limit - $batch->members->count() }} more members...
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
        <div class="mt-8">
            {{ $batches->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-orange-100 to-purple-100 rounded-2xl flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No batches found</h3>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                @if($filter === 'all')
                    You haven't joined any batches yet. Find a new batch to join!
                @elseif($filter === 'active')
                    You don't have any active batches waiting to be filled.
                @else
                    You don't have any closed or completed batches.
                @endif
            </p>
            @if($filter !== 'all')
                <div class="mt-6">
                    <button 
                        wire:click="$set('filter', 'all')"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                        View All Batches
                    </button>
                </div>
            @endif
        </div>
    @endif
    </div>
</div>