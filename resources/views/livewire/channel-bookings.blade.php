<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
       

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-semibold text-yellow-600">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Running</p>
                        <p class="text-2xl font-semibold text-green-600">{{ $stats['running'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Completed</p>
                        <p class="text-2xl font-semibold text-purple-600">{{ $stats['completed'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-wrap gap-2">
                <button wire:click="$set('filter', 'all')" 
                        class="px-4 py-2 rounded-md text-sm font-medium {{ $filter === 'all' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All Bookings
                </button>
                <button wire:click="$set('filter', 'pending')" 
                        class="px-4 py-2 rounded-md text-sm font-medium {{ $filter === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Pending ({{ $stats['pending'] }})
                </button>
                <button wire:click="$set('filter', 'accepted')" 
                        class="px-4 py-2 rounded-md text-sm font-medium {{ $filter === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Accepted
                </button>
                <button wire:click="$set('filter', 'running')" 
                        class="px-4 py-2 rounded-md text-sm font-medium {{ $filter === 'running' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Running
                </button>
                <button wire:click="$set('filter', 'completed')" 
                        class="px-4 py-2 rounded-md text-sm font-medium {{ $filter === 'completed' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Completed
                </button>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($bookings->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($bookings as $booking)
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $booking->title ?: 'Advertisement Booking' }}
                                        </h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($booking->status === 'accepted') bg-green-100 text-green-800
                                            @elseif($booking->status === 'running') bg-blue-100 text-blue-800
                                            @elseif($booking->status === 'completed') bg-purple-100 text-purple-800
                                            @elseif($booking->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-gray-600 mb-4">
                                        <div>
                                            <span class="font-medium">Channel:</span> {{ $booking->channel->name }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Advertiser:</span> {{ $booking->user->name }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Duration:</span> {{ $booking->duration_hours }} hours
                                        </div>
                                        <div>
                                            <span class="font-medium">Amount:</span> ₦{{ number_format($booking->total_amount) }}
                                        </div>
                                    </div>
                                    
                                    @if($booking->description)
                                        <p class="text-gray-700 mb-4">{{ $booking->description }}</p>
                                    @endif
                                    
                                    @if($booking->url)
                                        <div class="mb-4">
                                            <span class="text-sm font-medium text-gray-500">URL:</span>
                                            <a href="{{ $booking->url }}" target="_blank" class="text-blue-600 hover:text-blue-800 ml-2">{{ $booking->url }}</a>
                                        </div>
                                    @endif
                                    
                                    @if($booking->images)
                                        <div class="mb-4">
                                            <span class="text-sm font-medium text-gray-500 block mb-2">Images:</span>
                                            <div class="flex space-x-2">
                                                @foreach(json_decode($booking->images) as $image)
                                                    <img src="{{ Storage::url($image) }}" alt="Ad Image" class="w-16 h-16 object-cover rounded border">
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="text-xs text-gray-500">
                                        Booked: {{ $booking->created_at->format('M d, Y H:i') }}
                                        @if($booking->accepted_at)
                                            | Accepted: {{ $booking->accepted_at->format('M d, Y H:i') }}
                                        @endif
                                        @if($booking->started_at)
                                            | Started: {{ $booking->started_at->format('M d, Y H:i') }}
                                        @endif
                                        @if($booking->completed_at)
                                            | Completed: {{ $booking->completed_at->format('M d, Y H:i') }}
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="ml-6 flex flex-col space-y-2">
                                    @if($booking->status === 'pending')
                                        <button wire:click="acceptBooking({{ $booking->id }})" 
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Accept
                                        </button>
                                        <button wire:click="rejectBooking({{ $booking->id }})" 
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Reject
                                        </button>
                                    @elseif($booking->status === 'accepted')
                                        <button wire:click="startBooking({{ $booking->id }})" 
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Start Ad
                                        </button>
                                    @elseif($booking->status === 'running')
                                        <button wire:click="openProofModal({{ $booking->id }})" 
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            Submit Proof
                                        </button>
                                    @endif
                                    
                                    @if($booking->proof_screenshot && $booking->status === 'proof_submitted')
                                        <div class="text-xs text-gray-500">
                                            <p class="font-medium">Proof submitted</p>
                                            <p>Waiting for admin approval</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No bookings found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($filter === 'all')
                            You don't have any advertisement bookings yet.
                        @else
                            No bookings found with the selected filter.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Proof Submission Modal -->
    @if($showProofModal && $selectedBooking)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeProofModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="submitProof">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                        Submit Proof of Completion
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <!-- Proof Screenshot -->
                                        <div>
                                            <label for="proof_screenshot" class="block text-sm font-medium text-gray-700">Proof Screenshot *</label>
                                            <input type="file" 
                                                   wire:model="proof_screenshot" 
                                                   id="proof_screenshot" 
                                                   accept="image/*" 
                                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                            @error('proof_screenshot') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        
                                        <!-- Proof Description -->
                                        <div>
                                            <label for="proof_description" class="block text-sm font-medium text-gray-700">Description *</label>
                                            <textarea wire:model="proof_description" 
                                                      id="proof_description" 
                                                      rows="3" 
                                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                                      placeholder="Describe how the advertisement was completed..."></textarea>
                                            @error('proof_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    wire:loading.attr="disabled" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                                <span wire:loading.remove>Submit Proof</span>
                                <span wire:loading>Submitting...</span>
                            </button>
                            <button type="button" 
                                    wire:click="closeProofModal" 
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>