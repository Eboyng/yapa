<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Flash Messages -->
        <div id="success-message" class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-2xl p-4 animate-fade-in hidden">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-green-800 font-medium">Profile updated successfully!</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div id="error-message" class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-2xl p-4 animate-fade-in hidden">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <span class="text-red-800 font-medium">Error occurred!</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Profile Header -->
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

        <!-- Wallet Balances -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-orange-100 to-orange-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Credits</p>
                        <p class="text-base sm:text-lg lg:text-xl font-bold text-orange-600">{{ number_format($this->creditsBalance) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Naira</p>
                        <p class="text-base sm:text-lg lg:text-xl font-bold text-green-600">₦{{ number_format($this->nairaBalance, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Earnings</p>
                        <p class="text-base sm:text-lg lg:text-xl font-bold text-purple-600">₦{{ number_format($this->earningsBalance, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Tabs -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50">
                <nav class="flex overflow-x-auto">
                    <button onclick="setActiveTab('profile')" id="profileTabBtn" class="flex-shrink-0 px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-orange-600 hover:border-orange-300">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="whitespace-nowrap">Profile</span>
                        </div>
                    </button>
                    
                    <button onclick="setActiveTab('security')" id="securityTabBtn" class="flex-shrink-0 px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-orange-600 hover:border-orange-300">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span class="whitespace-nowrap">Security</span>
                        </div>
                    </button>
                    
                    <button onclick="setActiveTab('whatsapp')" id="whatsappTabBtn" class="flex-shrink-0 px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-orange-600 hover:border-orange-300">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785"/>
                            </svg>
                            <span class="whitespace-nowrap">WhatsApp</span>
                        </div>
                    </button>
                    
                    <button onclick="setActiveTab('referrals')" id="referralsTabBtn" class="flex-shrink-0 px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-orange-600 hover:border-orange-300">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="whitespace-nowrap">Referrals</span>
                        </div>
                    </button>
                    
                    <button onclick="setActiveTab('sharing')" id="sharingTabBtn" class="flex-shrink-0 px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-orange-600 hover:border-orange-300">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                            <span class="whitespace-nowrap">Batch Share</span>
                        </div>
                    </button>
                    
                    <button onclick="setActiveTab('integrations')" id="integrationsTabBtn" class="flex-shrink-0 px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-orange-600 hover:border-orange-300">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <span class="whitespace-nowrap">Apps</span>
                        </div>
                    </button>
                </nav>
            </div>

            <!-- Edit Profile Tab -->
            <div id="profileTab" class="p-4 sm:p-6 lg:p-8 tab-content">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Edit Profile Information
                </h2>
                
                <form wire:submit="updateProfile" class="space-y-4 sm:space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                            <input type="text" id="name" wire:model="name"
                                   class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                   placeholder="Enter your full name">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                            <select id="location" wire:model="location"
                                    class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                                <option value="">Select your state</option>
                                @foreach($nigerianStates as $state)
                                    <option value="{{ $state }}">{{ $state }}</option>
                                @endforeach
                            </select>
                            @error('location') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Interests -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-sm font-semibold text-gray-700">Interests (Max 5)</label>
                            <div class="text-xs sm:text-sm text-gray-500">
                                <span>{{ count($selectedInterests) }}</span>/5 selected
                            </div>
                        </div>
                        
                        <!-- Selected Interests Display -->
                        <div id="selectedInterests" class="mb-3 min-h-[2rem] flex flex-wrap gap-2">
                            @if(is_array($selectedInterests) && count($selectedInterests) > 0 && $availableInterests && $availableInterests->count() > 0)
                                @foreach($availableInterests->whereIn('id', $selectedInterests) as $interest)
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-800">
                                    {{ $interest->icon }} {{ $interest->name }}
                                    <button type="button" wire:click="$set('selectedInterests', {{ json_encode(array_values(array_diff($selectedInterests, [$interest->id]))) }})" class="ml-1 text-orange-600 hover:text-orange-800">×</button>
                                </span>
                                @endforeach
                            @endif
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3">
                            <!-- Available Interests -->
                            @if($availableInterests && $availableInterests->count() > 0)
                                @foreach($availableInterests as $interest)
                                 <label class="interest-option flex items-center p-2 sm:p-3 border-2 {{ in_array($interest->id, $selectedInterests ?? []) ? 'border-orange-300 bg-orange-50' : 'border-gray-200' }} rounded-xl cursor-pointer hover:bg-gray-50 transition-all duration-200 transform hover:scale-105">
                                     <input type="checkbox" class="interest-checkbox sr-only" value="{{ $interest->id }}" wire:model="selectedInterests" {{ in_array($interest->id, $selectedInterests ?? []) ? 'checked' : '' }}>
                                     <div class="flex items-center w-full">
                                         <span class="text-base sm:text-lg mr-2">{{ $interest->icon }}</span>
                                         <span class="text-xs sm:text-sm font-medium truncate">{{ $interest->name }}</span>
                                         @if(in_array($interest->id, $selectedInterests ?? []))
                                         <div class="ml-auto checkmark-container">
                                             <svg class="checkmark w-3 h-3 sm:w-4 sm:h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                                 <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                             </svg>
                                         </div>
                                         @endif
                                     </div>
                                 </label>
                                 @endforeach
                            @endif

                        </div>
                        @error('selectedInterests') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="px-6 sm:px-8 py-2 sm:py-3 text-sm sm:text-base bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50">
                            <span class="flex items-center" wire:loading.remove wire:target="updateProfile">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Profile
                            </span>
                            <span class="flex items-center" wire:loading wire:target="updateProfile">
                                <svg class="animate-spin w-3 h-3 sm:w-4 sm:h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Tab -->
            <div id="securityTab" class="p-4 sm:p-6 lg:p-8 tab-content hidden">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Security Settings
                </h2>
                
                <!-- Change Password -->
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-2xl p-4 sm:p-6 mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        Change Password
                    </h3>
                    
                    <form onsubmit="updatePassword(event)" class="space-y-3 sm:space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label for="currentPassword" class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                                <input type="password" id="currentPassword" 
                                       class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                       placeholder="Enter current password">
                            </div>
                            
                            <div>
                                <label for="newPassword" class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                                <input type="password" id="newPassword" 
                                       class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                       placeholder="Enter new password">
                            </div>
                        </div>
                        
                        <div>
                            <label for="newPasswordConfirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" id="newPasswordConfirmation" 
                                   class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                   placeholder="Confirm new password">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-105">
                                <span class="flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                    Update Password
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Security Info -->
                <div class="bg-blue-50 rounded-xl p-3 sm:p-4">
                    <div class="flex items-start">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium text-blue-900 text-sm sm:text-base">Password Requirements</h4>
                            <ul class="text-xs sm:text-sm text-blue-800 mt-1 space-y-1">
                                <li>• At least 8 characters long</li>
                                <li>• Contains uppercase and lowercase letters</li>
                                <li>• Includes at least one number</li>
                                <li>• Has at least one special character</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Tab -->
            <div id="whatsappTab" class="p-4 sm:p-6 lg:p-8 tab-content hidden">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785"/>
                    </svg>
                    WhatsApp Settings
                </h2>

                <!-- Current WhatsApp Number -->
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-2xl p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="font-semibold text-green-900 text-sm sm:text-base">Current WhatsApp Number</h3>
                            <p class="text-green-700 text-base sm:text-lg font-medium">+234 901 234 5678</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Change WhatsApp Number -->
                <div class="border border-orange-200 rounded-2xl p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex items-start justify-between mb-3 sm:mb-4">
                        <div class="flex-1">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900">Change WhatsApp Number</h3>
                            <p class="text-xs sm:text-sm text-orange-600 mt-1 flex items-center">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Costs 100 credits
                            </p>
                        </div>
                    </div>

                    <div id="numberChangeStep1" class="step-content">
                        <form onsubmit="initiateNumberChange(event)" class="space-y-3 sm:space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                <div>
                                    <label for="newWhatsappNumber" class="block text-sm font-semibold text-gray-700 mb-2">New WhatsApp Number</label>
                                    <input type="text" id="newWhatsappNumber" 
                                           class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                           placeholder="+234XXXXXXXXXX">
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                                    <input type="password" id="password" 
                                           class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                           placeholder="Enter your password">
                                </div>
                            </div>

                            <button type="submit" 
                                    class="w-full px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-105">
                                <span class="flex items-center justify-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Send OTP
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Notification Preferences -->
                <div class="border border-gray-200 rounded-2xl p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Notification Preferences</h3>
                    
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-green-50 rounded-xl">
                        <div class="flex items-center flex-1">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-500 rounded-xl flex items-center justify-center mr-3 flex-shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-green-900 text-sm sm:text-base">WhatsApp Notifications</h4>
                                <p class="text-xs sm:text-sm text-green-700">Receive important updates via WhatsApp</p>
                            </div>
                        </div>
                        
                        <label class="relative inline-flex items-center cursor-pointer ml-3">
                            <input type="checkbox" id="notifyWhatsapp" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-orange-500 peer-checked:to-purple-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Referrals Tab -->
            <div id="referralsTab" class="p-4 sm:p-6 lg:p-8 tab-content hidden">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Referral System
                </h2>
                
                <!-- Referral Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Referrals</p>
                                <p class="text-xl font-bold text-gray-900">{{ count($referredUsers) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Rewards</p>
                                <p class="text-xl font-bold text-gray-900">₦{{ number_format($totalReferralRewards, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-2xl p-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Referral Code</p>
                                <p class="text-lg font-bold text-gray-900">{{ $referralCode }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Referral Link -->
                <div class="border border-gray-200 rounded-2xl p-4 sm:p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Your Referral Link</h3>
                    <div class="flex items-center space-x-2">
                        <input type="text" value="{{ $referralLink }}" readonly 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                        <button onclick="copyReferralLink()" 
                                class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Share this link to earn rewards when people sign up and make deposits!</p>
                </div>
                
                <!-- Referred Users -->
                <div class="border border-gray-200 rounded-2xl p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Referred Users</h3>
                        @if(!$showReferralSection)
                            <button wire:click="toggleReferralSection" 
                                    class="text-sm text-blue-600 hover:text-blue-700">
                                Show Details
                            </button>
                        @endif
                    </div>
                    
                    @if($showReferralSection)
                        @if($isLoadingReferrals)
                            <div class="flex justify-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                            </div>
                        @else
                            @if(count($referredUsers) > 0)
                                <div class="space-y-3">
                                    @foreach($referredUsers as $user)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-sm font-semibold text-green-600">{{ substr($user->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                                    <p class="text-xs text-gray-500">Joined {{ $user->created_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <span class="text-sm text-green-600 font-medium">Active</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-gray-500">No referrals yet</p>
                                    <p class="text-sm text-gray-400">Start sharing your referral link to earn rewards!</p>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
            
            <!-- Batch Share Tab -->
            <div id="batchShareTab" class="p-4 sm:p-6 lg:p-8 tab-content hidden">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                    </svg>
                    Batch Sharing
                </h2>
                
                <!-- Batch Selection -->
                <div class="border border-gray-200 rounded-2xl p-4 sm:p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Select Batch to Share</h3>
                    
                    @if($isLoadingBatches)
                        <div class="flex justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500"></div>
                        </div>
                    @else
                        @if(count($openBatches) > 0)
                            <div class="space-y-3">
                                @foreach($openBatches as $batch)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors cursor-pointer"
                                         wire:click="$set('selectedBatch', {{ $batch->id }})">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <input type="radio" name="selectedBatch" value="{{ $batch->id }}" 
                                                       @if($selectedBatch == $batch->id) checked @endif
                                                       class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500">
                                                <div class="ml-3">
                                                    <p class="font-medium text-gray-900">{{ $batch->name }}</p>
                                                    <p class="text-sm text-gray-500">{{ $batch->description }}</p>
                                                    <p class="text-xs text-gray-400">Ends: {{ $batch->end_date->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-gray-900">{{ $batch->members_count }}/{{ $batch->max_members }}</p>
                                                <p class="text-xs text-gray-500">Members</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <p class="text-gray-500">No open batches available</p>
                                <p class="text-sm text-gray-400">Check back later for new batches to share!</p>
                            </div>
                        @endif
                    @endif
                </div>
                
                <!-- Share Actions -->
                @if($selectedBatch)
                    <div class="border border-gray-200 rounded-2xl p-4 sm:p-6 mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3">Share Batch</h3>
                        
                        @if($isGeneratingShareUrl)
                            <div class="flex justify-center py-4">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-500"></div>
                            </div>
                        @else
                            @if($shareUrl)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Share URL</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="text" value="{{ $shareUrl }}" readonly 
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                        <button onclick="copyToClipboard('{{ $shareUrl }}')" 
                                                class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <button wire:click="shareBatch('whatsapp')" 
                                        class="flex items-center justify-center px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                    WhatsApp
                                </button>
                                
                                <button wire:click="shareBatch('facebook')" 
                                        class="flex items-center justify-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    Facebook
                                </button>
                                
                                <button wire:click="shareBatch('twitter')" 
                                        class="flex items-center justify-center px-4 py-3 bg-blue-400 hover:bg-blue-500 text-white rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                    Twitter
                                </button>
                                
                                <button wire:click="shareBatch('copy')" 
                                        class="flex items-center justify-center px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Copy Link
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Sharing Progress -->
                    <div class="border border-gray-200 rounded-2xl p-4 sm:p-6">
                        <h3 class="font-semibold text-gray-900 mb-3">Sharing Progress</h3>
                        
                        @if($showBatchShareSection)
                            @php
                                $progress = $this->getBatchShareProgress($selectedBatch);
                            @endphp
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-green-600">{{ $progress['whatsapp'] ?? 0 }}</p>
                                    <p class="text-sm text-gray-600">WhatsApp</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-blue-600">{{ $progress['facebook'] ?? 0 }}</p>
                                    <p class="text-sm text-gray-600">Facebook</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-blue-400">{{ $progress['twitter'] ?? 0 }}</p>
                                    <p class="text-sm text-gray-600">Twitter</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-gray-600">{{ $progress['copy'] ?? 0 }}</p>
                                    <p class="text-sm text-gray-600">Copy Link</p>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-purple-50 rounded-lg">
                                <p class="text-sm text-purple-700">
                                    <strong>Total Shares:</strong> {{ array_sum($progress) }} | 
                                    <strong>Potential Reward:</strong> ₦{{ number_format(array_sum($progress) * 50, 2) }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Integrations Tab -->
            <div id="integrationsTab" class="p-4 sm:p-6 lg:p-8 tab-content hidden">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Third-party Integrations
                </h2>
                
                <!-- Google People API -->
                <div class="border border-gray-200 rounded-2xl p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-7 sm:h-7 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm sm:text-base">Google Contacts</h3>
                                <p class="text-xs sm:text-sm text-gray-600">
                                    Connect to sync your Google contacts and improve networking
                                </p>
                            </div>
                        </div>
                        
                        <button onclick="connectGoogle()" 
                                class="px-4 sm:px-6 py-2 sm:py-3 text-xs sm:text-sm bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-105 ml-3">
                            <span class="flex items-center whitespace-nowrap">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                Connect
                            </span>
                        </button>
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
    animation: fade-in 0.3s ease-out;
}

/* Tab transition effects */
.tab-content {
    transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
}

.tab-content.hidden {
    opacity: 0;
    transform: translateY(10px);
}

.tab-content:not(.hidden) {
    opacity: 1;
    transform: translateY(0);
}

/* Interest selection styles */
.interest-option {
    position: relative;
    overflow: hidden;
}

.interest-option:has(.interest-checkbox:checked) {
    border-color: #f97316;
    background-color: #fff7ed;
}

.interest-option:has(.interest-checkbox:checked) .checkmark {
    opacity: 1;
    transform: scale(1);
}

.interest-option .checkmark {
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.2s ease-in-out;
}

.interest-option.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

/* Custom toggle switch */
.peer:checked + div {
    background: linear-gradient(to right, #f97316, #a855f7);
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: linear-gradient(to right, #f97316, #a855f7);
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to right, #ea580c, #9333ea);
}

/* Ripple animation */
@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

/* Pulse animation */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

/* Mobile-specific adjustments */
@media (max-width: 640px) {
    .grid-cols-2 {
        gap: 0.5rem;
    }
    
    .interest-option {
        padding: 0.5rem;
    }
    
    .interest-option span {
        font-size: 0.75rem;
    }
}
</style>

<script>
// Tab functionality
let currentActiveTab = 'profile';

function setActiveTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active styles from all buttons
    const tabButtons = document.querySelectorAll('[id$="TabBtn"]');
    tabButtons.forEach(button => {
        button.classList.remove('text-orange-600', 'border-orange-500', 'bg-orange-50');
        button.classList.add('text-gray-500');
    });
    
    // Show selected tab content
    const selectedTab = document.getElementById(tabName + 'Tab');
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
    }
    
    // Add active styles to selected button
    const selectedButton = document.getElementById(tabName + 'TabBtn');
    if (selectedButton) {
        selectedButton.classList.remove('text-gray-500');
        selectedButton.classList.add('text-orange-600', 'border-orange-500', 'bg-orange-50');
    }
    
    currentActiveTab = tabName;
}

// Interest management
const interestData = {
    business: { name: 'Business', icon: '🎯' },
    technology: { name: 'Technology', icon: '💻' },
    design: { name: 'Design', icon: '🎨' },
    marketing: { name: 'Marketing', icon: '📢' },
    finance: { name: 'Finance', icon: '💰' },
    health: { name: 'Health', icon: '🏥' },
    education: { name: 'Education', icon: '📚' },
    sports: { name: 'Sports', icon: '⚽' },
    music: { name: 'Music', icon: '🎵' },
    travel: { name: 'Travel', icon: '✈️' },
    food: { name: 'Food', icon: '🍽️' }
};

let selectedInterests = new Set(['business', 'technology', 'design']);
let showingAllInterests = false;

function updateInterestDisplay() {
    const selectedContainer = document.getElementById('selectedInterests');
    const selectedCountElement = document.getElementById('selectedCount');
    
    // Update selected interests display
    selectedContainer.innerHTML = '';
    selectedInterests.forEach(interestId => {
        const interest = interestData[interestId];
        const badge = document.createElement('span');
        badge.className = 'inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-800';
        badge.innerHTML = `
            ${interest.icon} ${interest.name}
            <button type="button" onclick="removeInterest('${interestId}')" class="ml-1 text-orange-600 hover:text-orange-800">×</button>
        `;
        selectedContainer.appendChild(badge);
    });
    
    // Update count
    selectedCountElement.textContent = selectedInterests.size;
    
    // Update checkboxes and borders
    document.querySelectorAll('.interest-option').forEach(option => {
        const interestId = option.dataset.interest;
        const checkbox = option.querySelector('.interest-checkbox');
        const isSelected = selectedInterests.has(interestId);
        
        checkbox.checked = isSelected;
        
        if (isSelected) {
            option.classList.add('border-orange-500', 'bg-orange-50');
            option.classList.remove('border-gray-200');
        } else {
            option.classList.remove('border-orange-500', 'bg-orange-50');
            option.classList.add('border-gray-200');
        }
        
        // Disable/enable based on selection limit
        if (selectedInterests.size >= 5 && !isSelected) {
            option.classList.add('disabled');
        } else {
            option.classList.remove('disabled');
        }
    });
}

function removeInterest(interestId) {
    selectedInterests.delete(interestId);
    updateInterestDisplay();
}

function toggleMoreInterests() {
    const hiddenOptions = document.querySelectorAll('.interest-option.hidden');
    const showMoreBtn = document.getElementById('showMoreInterests');
    
    if (showingAllInterests) {
        hiddenOptions.forEach(option => option.classList.add('hidden'));
        showMoreBtn.textContent = 'Show more interests';
        showingAllInterests = false;
    } else {
        hiddenOptions.forEach(option => option.classList.remove('hidden'));
        showMoreBtn.textContent = 'Show fewer interests';
        showingAllInterests = true;
    }
}

// Handle interest selection
document.addEventListener('click', function(e) {
    const interestOption = e.target.closest('.interest-option');
    if (interestOption && !interestOption.classList.contains('disabled')) {
        const interestId = interestOption.dataset.interest;
        const checkbox = interestOption.querySelector('.interest-checkbox');
        
        if (selectedInterests.has(interestId)) {
            selectedInterests.delete(interestId);
        } else {
            if (selectedInterests.size < 5) {
                selectedInterests.add(interestId);
            }
        }
        
        updateInterestDisplay();
    }
});

// Form submissions
function updateProfile(event) {
    event.preventDefault();
    const successMsg = document.getElementById('success-message');
    successMsg.classList.remove('hidden');
    setTimeout(() => successMsg.classList.add('hidden'), 3000);
}

function updatePassword(event) {
    event.preventDefault();
    const successMsg = document.getElementById('success-message');
    successMsg.querySelector('span').textContent = 'Password updated successfully!';
    successMsg.classList.remove('hidden');
    setTimeout(() => successMsg.classList.add('hidden'), 3000);
}

function initiateNumberChange(event) {
    event.preventDefault();
    const successMsg = document.getElementById('success-message');
    successMsg.querySelector('span').textContent = 'OTP sent to your new WhatsApp number!';
    successMsg.classList.remove('hidden');
    setTimeout(() => successMsg.classList.add('hidden'), 3000);
}

function connectGoogle() {
    const successMsg = document.getElementById('success-message');
    successMsg.querySelector('span').textContent = 'Redirecting to Google authentication...';
    successMsg.classList.remove('hidden');
    setTimeout(() => successMsg.classList.add('hidden'), 3000);
}

// Referral and Batch Sharing Functions
function copyReferralLink() {
    const referralInput = document.querySelector('input[value*="/register?ref="]');
    if (referralInput) {
        copyToClipboard(referralInput.value);
        showNotification('Referral link copied to clipboard!', 'success');
    }
}

function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Link copied to clipboard!', 'success');
        }).catch(() => {
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.top = '0';
    textArea.style.left = '0';
    textArea.style.position = 'fixed';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Link copied to clipboard!', 'success');
    } catch (err) {
        showNotification('Failed to copy link', 'error');
    }
    
    document.body.removeChild(textArea);
}

function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                }
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    setActiveTab('profile');
    updateInterestDisplay();
    
    // Add smooth scrolling behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Add intersection observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe wallet cards for scroll animations
    const walletCards = document.querySelectorAll('.grid.grid-cols-1.sm\\:grid-cols-3 > div');
    walletCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.6s ease-out ${index * 0.1}s, transform 0.6s ease-out ${index * 0.1}s`;
        observer.observe(card);
    });
    
    // Add click ripple effect for buttons
    const buttons = document.querySelectorAll('button, a[class*="bg-gradient"]');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.disabled) return;
            
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add form field focus animations
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.2s ease-out';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
</div>