<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">My Channel Applications</h1>
        <p class="text-gray-600">Manage your channel advertising applications</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Approved</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Earnings</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['total_earnings']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" wire:model.live="search" placeholder="Search applications..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Clear Filters -->
            <div class="flex items-end">
                <button wire:click="clearFilters" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md hover:bg-gray-50">
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Applications List -->
    <div class="space-y-4 mb-6">
        @forelse($applications as $application)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <!-- Application Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $application->channelAd->title }}</h3>
                            <p class="text-sm text-gray-600">Channel: {{ $application->channel->name }}</p>
                        </div>
                        
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'proof_submitted' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'disputed' => 'bg-purple-100 text-purple-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                        </span>
                    </div>
                    
                    <!-- Application Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span class="font-medium text-green-600">₦{{ number_format($application->channelAd->payment_per_channel) }}</span>
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Applied: {{ $application->applied_at->format('M j, Y') }}
                        </div>
                        
                        @if($application->channelAd->end_date)
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Deadline: {{ $application->channelAd->end_date->format('M j, Y') }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Rejection Reason -->
                    @if($application->status === 'rejected' && $application->rejection_reason)
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                            <p class="text-sm font-medium text-red-800 mb-1">Rejection Reason:</p>
                            <p class="text-sm text-red-700">{{ $application->rejection_reason }}</p>
                        </div>
                    @endif
                    
                    <!-- Admin Notes -->
                    @if($application->admin_notes)
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <p class="text-sm font-medium text-blue-800 mb-1">Admin Notes:</p>
                            <p class="text-sm text-blue-700">{{ $application->admin_notes }}</p>
                        </div>
                    @endif
                    
                    <!-- Dispute Information -->
                    @if($application->status === 'disputed')
                        <div class="mb-4 p-3 bg-purple-50 border border-purple-200 rounded-md">
                            <p class="text-sm font-medium text-purple-800 mb-1">Dispute Reason:</p>
                            <p class="text-sm text-purple-700">{{ $application->dispute_reason }}</p>
                            @if($application->dispute_resolution)
                                <p class="text-sm font-medium text-purple-800 mb-1 mt-2">Resolution:</p>
                                <p class="text-sm text-purple-700">{{ $application->dispute_resolution }}</p>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Proof of Work -->
                    @if($application->proof_screenshot || $application->proof_description)
                        <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-md">
                            <p class="text-sm font-medium text-gray-800 mb-2">Proof of Work:</p>
                            @if($application->proof_description)
                                <p class="text-sm text-gray-700 mb-2">{{ $application->proof_description }}</p>
                            @endif
                            @if($application->proof_screenshot)
                                <img src="{{ Storage::url($application->proof_screenshot) }}" 
                                     alt="Proof screenshot" 
                                     class="max-w-xs rounded-lg shadow-sm">
                            @endif
                        </div>
                    @endif
                    
                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Last updated: {{ $application->updated_at->diffForHumans() }}
                        </div>
                        
                        <div class="flex space-x-3">
                            <!-- Submit Proof Button -->
                            @if($application->status === 'approved' && !$application->proof_screenshot)
                                <button wire:click="$set('selectedApplication', {{ $application->id }})" 
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Submit Proof
                                </button>
                            @endif
                            
                            <!-- Submit Dispute Button -->
                            @if(in_array($application->status, ['approved', 'proof_submitted']) && $application->status !== 'disputed')
                                <button wire:click="$set('disputeApplication', {{ $application->id }})" 
                                        class="inline-flex items-center px-3 py-2 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    Submit Dispute
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No applications found</h3>
                <p class="mt-1 text-sm text-gray-500">You haven't applied to any channel ads yet.</p>
                <div class="mt-6">
                    <a href="{{ route('channel-ads.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Browse Channel Ads
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Submit Proof Modal -->
    @if($selectedApplication)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="$set('selectedApplication', null)">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Submit Proof of Work</h3>
                    
                    <form wire:submit="submitProof">
                        <!-- Proof Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea wire:model="proofDescription" 
                                      rows="3" 
                                      placeholder="Describe what you did to complete this ad..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('proofDescription') border-red-500 @enderror"></textarea>
                            @error('proofDescription')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Proof Screenshot -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Screenshot</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md @error('proofScreenshot') border-red-500 @enderror">
                                <div class="space-y-1 text-center">
                                    @if ($proofScreenshot)
                                        <div class="mb-4">
                                            <img src="{{ $proofScreenshot->temporaryUrl() }}" class="mx-auto h-32 w-auto rounded-lg shadow-md">
                                            <button type="button" wire:click="$set('proofScreenshot', null)" class="mt-2 text-sm text-red-600 hover:text-red-800">
                                                Remove
                                            </button>
                                        </div>
                                    @else
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @endif
                                    <div class="flex text-sm text-gray-600">
                                        <label for="proofScreenshot" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            <span>Upload screenshot</span>
                                            <input id="proofScreenshot" wire:model="proofScreenshot" type="file" accept="image/*" class="sr-only">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('proofScreenshot')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="$set('selectedApplication', null)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                                Cancel
                            </button>
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    wire:target="submitProof,proofScreenshot"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md disabled:opacity-50">
                                <span wire:loading.remove wire:target="submitProof">Submit Proof</span>
                                <span wire:loading wire:target="submitProof">Submitting...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Submit Dispute Modal -->
    @if($disputeApplication)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="$set('disputeApplication', null)">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Submit Dispute</h3>
                    
                    <form wire:submit="submitDispute">
                        <!-- Dispute Reason -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Dispute</label>
                            <textarea wire:model="disputeReason" 
                                      rows="4" 
                                      placeholder="Explain why you are disputing this application..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('disputeReason') border-red-500 @enderror"></textarea>
                            @error('disputeReason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="$set('disputeApplication', null)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                                Cancel
                            </button>
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    wire:target="submitDispute"
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md disabled:opacity-50">
                                <span wire:loading.remove wire:target="submitDispute">Submit Dispute</span>
                                <span wire:loading wire:target="submitDispute">Submitting...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Success!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Error!</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    <div class="mt-6">
        {{ $applications->links() }}
    </div>
</div>