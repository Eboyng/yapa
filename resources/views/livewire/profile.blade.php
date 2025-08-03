<div class="min-h-screen bg-white">
    <div class="py-4 sm:py-8 lg:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6 lg:space-y-8">
            
            <!-- Flash Messages -->
            @if (session()->has('success'))
                <div class="bg-green-50 border-l-4 border-green-400 text-green-800 px-3 py-2 sm:px-4 sm:py-3 rounded-r-lg mb-3 sm:mb-4 transition-all duration-300 hover:bg-green-100">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm sm:text-base">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-50 border-l-4 border-red-400 text-red-800 px-3 py-2 sm:px-4 sm:py-3 rounded-r-lg mb-3 sm:mb-4 transition-all duration-300 hover:bg-red-100">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm sm:text-base">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if (session()->has('info'))
                <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-800 px-3 py-2 sm:px-4 sm:py-3 rounded-r-lg mb-3 sm:mb-4 transition-all duration-300 hover:bg-blue-100">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm sm:text-base">{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-orange-50 to-purple-50 border border-orange-100 rounded-xl sm:rounded-2xl overflow-hidden">
                <div class="p-4 sm:p-6 lg:p-8">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-6">
                        <div class="flex-shrink-0 relative group">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 lg:w-28 lg:h-28 rounded-full ring-4 ring-orange-200 transition-all duration-300 group-hover:ring-purple-300 overflow-hidden">
                                <img class="w-full h-full object-cover" 
                                     src="{{ $this->avatarUrl }}" 
                                     alt="{{ $user->name }}">
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 sm:w-8 sm:h-8 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="flex-1 text-center sm:text-left">
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-1">{{ $user->name }}</h1>
                            <p class="text-sm sm:text-base text-gray-600 mb-2">{{ $user->email }}</p>
                            @if($user->location)
                                <div class="flex items-center justify-center sm:justify-start text-xs sm:text-sm text-gray-500">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $user->location }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="w-full sm:w-auto">
                            <div class="grid grid-cols-3 gap-2 sm:gap-4">
                                <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-4 text-center border border-orange-100 hover:border-orange-200 transition-all duration-200">
                                    <div class="text-lg sm:text-xl lg:text-2xl font-bold text-orange-600">{{ number_format($this->creditsBalance) }}</div>
                                    <div class="text-xs text-gray-500">Credits</div>
                                </div>
                                <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-4 text-center border border-purple-100 hover:border-purple-200 transition-all duration-200">
                                    <div class="text-lg sm:text-xl lg:text-2xl font-bold text-purple-600">₦{{ number_format($this->nairaBalance, 2) }}</div>
                                    <div class="text-xs text-gray-500">Naira</div>
                                </div>
                                <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-4 text-center border border-orange-100 hover:border-orange-200 transition-all duration-200">
                                    <div class="text-lg sm:text-xl lg:text-2xl font-bold text-orange-600">₦{{ number_format($this->earningsBalance, 2) }}</div>
                                    <div class="text-xs text-gray-500">Earnings</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div class="bg-white border border-orange-100 rounded-xl sm:rounded-2xl overflow-hidden hover:border-orange-200 transition-all duration-300">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-xl sm:text-2xl font-bold text-gray-900">{{ $this->batchParticipationCount }}</div>
                                <div class="text-xs sm:text-sm text-gray-600">Batches Joined</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-purple-100 rounded-xl sm:rounded-2xl overflow-hidden hover:border-purple-200 transition-all duration-300">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-xl sm:text-2xl font-bold text-gray-900">{{ $this->completedAdTasksCount }}</div>
                                <div class="text-xs sm:text-sm text-gray-600">Completed Tasks</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="bg-white border border-gray-100 rounded-xl sm:rounded-2xl overflow-hidden">
                <div class="border-b border-gray-100 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Profile Information</h3>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6">
                    <form wire:submit.prevent="updateProfile">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div class="space-y-2">
                                <label for="name" class="block text-xs sm:text-sm font-medium text-gray-700">Full Name</label>
                                <input type="text" id="name" wire:model="name" 
                                       class="w-full px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base rounded-lg border border-gray-200 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition-all duration-200">
                                @error('name') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="location" class="block text-xs sm:text-sm font-medium text-gray-700">Location</label>
                                <select id="location" wire:model="location" 
                                        class="w-full px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base rounded-lg border border-gray-200 focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all duration-200">
                                    <option value="">Select your state</option>
                                    @foreach($nigerianStates as $state)
                                        <option value="{{ $state }}">{{ $state }}</option>
                                    @endforeach
                                </select>
                                @error('location') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Interests Section -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-xs sm:text-sm font-medium text-gray-700">Interests (Max 5)</label>
                                @if(!$editingInterests && count($this->userInterests) > 0)
                                    <button type="button" wire:click="toggleInterestEdit" 
                                            class="text-xs sm:text-sm text-orange-600 hover:text-orange-700 font-medium">
                                        Edit Interests
                                    </button>
                                @endif
                            </div>
                            
                            @if($editingInterests)
                                <!-- Interest Selection Form -->
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 mb-4">
                                    @foreach($availableInterests as $interest)
                                        <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 transition-colors duration-150 cursor-pointer">
                                            <input type="checkbox" 
                                                   wire:model="selectedInterests" 
                                                   value="{{ $interest->id }}" 
                                                   class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500 focus:ring-2">
                                            <span class="ml-2 text-xs sm:text-sm text-gray-700">{{ $interest->display_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    <button type="button" wire:click="toggleInterestEdit" 
                                            class="text-xs sm:text-sm text-gray-600 hover:text-gray-700 font-medium">
                                        Cancel
                                    </button>
                                    <span class="text-xs text-gray-500">|
                                    <span class="ml-2 text-xs text-gray-500">{{ count($selectedInterests) }}/5 selected</span>
                                </div>
                                @error('selectedInterests') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                            @else
                                <!-- Display Selected Interests -->
                                @if(count($this->userInterests) > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($this->userInterests as $interest)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                {{ $interest->display_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500 mb-3">No interests selected yet.</div>
                                    <button type="button" wire:click="toggleInterestEdit" 
                                            class="text-xs sm:text-sm bg-orange-100 text-orange-700 px-3 py-2 rounded-lg hover:bg-orange-200 transition-colors duration-150">
                                        Add Interests
                                    </button>
                                @endif
                            @endif
                        </div>

                        <div class="mt-6">
                            <button type="submit" 
                                    class="w-full sm:w-auto bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium py-2 px-6 sm:py-3 sm:px-8 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 text-sm sm:text-base"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="updateProfile">Update Profile</span>
                                <span wire:loading wire:target="updateProfile">Updating...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="bg-white border border-gray-100 rounded-xl sm:rounded-2xl overflow-hidden">
                <div class="border-b border-gray-100 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Security Settings</h3>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6">
                    <!-- Change Password -->
                    <div class="mb-6 sm:mb-8">
                        <h4 class="text-sm sm:text-base font-medium text-gray-800 mb-3 sm:mb-4">Change Password</h4>
                        <form wire:submit.prevent="updatePassword">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                <div class="space-y-2">
                                    <label for="current_password" class="block text-xs sm:text-sm font-medium text-gray-700">Current Password</label>
                                    <input type="password" id="current_password" wire:model="current_password" 
                                           class="w-full px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base rounded-lg border border-gray-200 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all duration-200">
                                    @error('current_password') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="new_password" class="block text-xs sm:text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" id="new_password" wire:model="new_password" 
                                           class="w-full px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base rounded-lg border border-gray-200 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all duration-200">
                                    @error('new_password') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="confirm_password" class="block text-xs sm:text-sm font-medium text-gray-700">Confirm Password</label>
                                    <input type="password" id="confirm_password" wire:model="confirm_password" 
                                           class="w-full px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base rounded-lg border border-gray-200 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all duration-200">
                                    @error('confirm_password') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" 
                                        class="w-full sm:w-auto bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium py-2 px-6 sm:py-3 sm:px-8 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 text-sm sm:text-base"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                                    <span wire:loading wire:target="updatePassword">Updating...</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Email Verification -->
                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between space-y-4 sm:space-y-0">
                            <div>
                                <h4 class="text-sm sm:text-base font-medium text-gray-800">Email Verification</h4>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1">Enable email verification for enhanced security</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                @if($user->hasVerifiedEmail())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Verified
                                    </span>
                                @else
                                    <button wire:click="resendEmailVerification" 
                                            class="text-orange-600 hover:text-orange-800 text-xs sm:text-sm font-medium hover:underline transition-colors duration-150">
                                        Resend Verification
                                    </button>
                                @endif
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="emailVerificationEnabled" wire:change="toggleEmailVerification" class="sr-only peer">
                                    <div class="w-10 h-5 sm:w-11 sm:h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 sm:after:h-5 sm:after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Settings -->
            <div class="bg-white border border-gray-100 rounded-xl sm:rounded-2xl overflow-hidden">
                <div class="border-b border-gray-100 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.63"/>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">WhatsApp Settings</h3>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6">
                    <!-- Current WhatsApp Number -->
                    <div class="mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between space-y-4 sm:space-y-0">
                            <div>
                                <h4 class="text-sm sm:text-base font-medium text-gray-800">Current WhatsApp Number</h4>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ $user->whatsapp_number ?: 'Not set' }}</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="notifyWhatsapp" wire:change="toggleNotifications" class="sr-only peer">
                                    <div class="w-10 h-5 sm:w-11 sm:h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 sm:after:h-5 sm:after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                    <span class="ml-3 text-xs sm:text-sm font-medium text-gray-700">Enable Notifications</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Change WhatsApp Number -->
                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex items-center mb-3 sm:mb-4">
                            <h4 class="text-sm sm:text-base font-medium text-gray-800">Change WhatsApp Number</h4>
                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                100 Credits
                            </span>
                        </div>
                        
                        @if(!$otpSent)
                            <form wire:submit.prevent="initiateNumberChange">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="newWhatsappNumber" class="block text-xs sm:text-sm font-medium text-gray-700">New WhatsApp Number</label>
                                        <input type="text" id="newWhatsappNumber" wire:model="newWhatsappNumber" 
                                               placeholder="+234XXXXXXXXXX"
                                               class="w-full px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base rounded-lg border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 transition-all duration-200">
                                        @error('newWhatsappNumber') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700">Confirm Password</label>
                                        <input type="password" id="password" wire:model="password" 
                                               class="w-full px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base rounded-lg border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 transition-all duration-200">
                                        @error('password') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" 
                                            class="w-full sm:w-auto bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium py-2 px-6 sm:py-3 sm:px-8 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 text-sm sm:text-base"
                                            wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="initiateNumberChange">Send OTP</span>
                                        <span wire:loading wire:target="initiateNumberChange">Sending...</span>
                                    </button>
                                </div>
                            </form>
                        @else
                            <form wire:submit.prevent="verifyNumberChange">
                                <div class="mb-4 space-y-2">
                                    <label for="otp" class="block text-xs sm:text-sm font-medium text-gray-700">Enter OTP</label>
                                    <input type="text" id="otp" wire:model="otp" 
                                           placeholder="6-digit OTP"
                                           class="w-full sm:w-1/3 px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base rounded-lg border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 transition-all duration-200">
                                    @error('otp') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                                    <button type="submit" 
                                            class="w-full sm:w-auto bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium py-2 px-6 sm:py-3 sm:px-8 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 text-sm sm:text-base"
                                            wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="verifyNumberChange">Verify & Update</span>
                                        <span wire:loading wire:target="verifyNumberChange">Verifying...</span>
                                    </button>
                                    
                                    <button type="button" wire:click="resetNumberChangeForm" 
                                            class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 sm:py-3 sm:px-8 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 text-sm sm:text-base">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Google Integration -->
            <div class="bg-white border border-gray-100 rounded-xl sm:rounded-2xl overflow-hidden">
                <div class="border-b border-gray-100 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Google Integration</h3>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between space-y-4 sm:space-y-0">
                        <div>
                            <h4 class="text-sm sm:text-base font-medium text-gray-800">Google Account</h4>
                            <p class="text-xs sm:text-sm text-gray-600 mt-1">
                                @if($googleConnected)
                                    Your Google account is connected
                                @else
                                    Connect your Google account for enhanced features
                                @endif
                            </p>
                        </div>
                        <div>
                            @if($googleConnected)
                                <button wire:click="disconnectGoogle" 
                                        class="w-full sm:w-auto bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium py-2 px-6 sm:py-3 sm:px-8 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 text-sm sm:text-base"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="disconnectGoogle">Disconnect</span>
                                    <span wire:loading wire:target="disconnectGoogle">Disconnecting...</span>
                                </button>
                            @else
                                <button wire:click="connectGoogle" 
                                        class="w-full sm:w-auto bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium py-2 px-6 sm:py-3 sm:px-8 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 text-sm sm:text-base"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="connectGoogle">Connect Google</span>
                                    <span wire:loading wire:target="connectGoogle">Connecting...</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateInterests() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const interests = [];
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    interests.push(checkbox.value);
                }
            });
            
            @this.set('selectedInterests', interests);
        }

        // Listen for Google OAuth events
        window.addEventListener('google-oauth-redirect', event => {
            console.log('Google OAuth redirect event received:', event.detail);
            
            if (!event.detail || !event.detail.url) {
                console.error('Google OAuth redirect event missing URL:', event.detail);
                alert('Error: Missing Google OAuth URL. Please try again.');
                return;
            }
            
            try {
                console.log('Redirecting to Google OAuth URL:', event.detail.url);
                window.location.href = event.detail.url;
            } catch (error) {
                console.error('Error redirecting to Google OAuth:', error);
                alert('Error redirecting to Google. Please try again.');
            }
        });

        window.addEventListener('google-oauth-success', event => {
            console.log('Google OAuth success event received:', event.detail);
            
            const message = event.detail?.message || 'Google account connected successfully!';
            alert(message);
            
            // Reload page to reflect changes
            setTimeout(() => {
                location.reload();
            }, 1000);
        });

        window.addEventListener('google-oauth-error', event => {
            console.error('Google OAuth error event received:', event.detail);
            
            const message = event.detail?.message || 'An error occurred with Google OAuth. Please try again.';
            alert(message);
            
            // Reset any loading states
            const connectButton = document.querySelector('[wire\\:click="connectGoogle"]');
            if (connectButton) {
                connectButton.disabled = false;
            }
        });

        // Add error handling for Livewire errors
        document.addEventListener('livewire:error', event => {
            console.error('Livewire error:', event.detail);
            alert('A system error occurred. Please check the console and try again.');
        });

        // Log when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Profile page loaded successfully');
        });
    </script>
</div>