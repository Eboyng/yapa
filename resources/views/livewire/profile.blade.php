<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
            <p class="mt-2 text-gray-600">Manage your account information and preferences</p>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-500 hover:text-red-700">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Overview -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-center">
                        <!-- Avatar -->
                        <div class="mx-auto h-24 w-24 rounded-full bg-gradient-to-r from-purple-400 to-orange-400 flex items-center justify-center text-white text-2xl font-bold mb-4">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        @if($user->location)
                            <p class="text-sm text-gray-500 mt-1">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $user->location }}
                            </p>
                        @endif
                    </div>

                    <!-- Wallet Balances -->
                    <div class="mt-6 space-y-3">
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-orange-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Credits</span>
                            </div>
                            <span class="text-sm font-bold text-orange-600">{{ number_format($this->creditsBalance) }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Naira Balance</span>
                            </div>
                            <span class="text-sm font-bold text-green-600">₦{{ number_format($this->nairaBalance, 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-purple-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Earnings</span>
                            </div>
                            <span class="text-sm font-bold text-purple-600">₦{{ number_format($this->earningsBalance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Edit Profile -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Edit Profile</h2>
                    
                    <form wire:submit.prevent="updateProfile" class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" id="name" wire:model="name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                                   placeholder="Enter your full name">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <select id="location" wire:model="location" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                                <option value="">Select your state</option>
                                @foreach($nigerianStates as $state)
                                    <option value="{{ $state }}">{{ $state }}</option>
                                @endforeach
                            </select>
                            @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Interests -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Interests (Max 5)</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($availableInterests as $interest)
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors
                                                  {{ in_array($interest->id, $selectedInterests) ? 'border-orange-500 bg-orange-50' : 'border-gray-300' }}">
                                        <input type="checkbox" wire:model="selectedInterests" value="{{ $interest->id }}"
                                               class="sr-only">
                                        <div class="flex items-center">
                                            <span class="text-lg mr-2">{{ $interest->icon }}</span>
                                            <span class="text-sm font-medium">{{ $interest->name }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedInterests') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50"
                                    wire:loading.attr="disabled" wire:target="updateProfile">
                                <span wire:loading.remove wire:target="updateProfile">Update Profile</span>
                                <span wire:loading wire:target="updateProfile">Updating...</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change WhatsApp Number -->
                <div class="bg-white rounded-lg shadow-md p-6" x-data="{ step: @entangle('otpSent') ? 2 : 1 }">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Change WhatsApp Number</h2>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Current WhatsApp Number: <span class="font-medium">{{ $user->whatsapp_number }}</span></p>
                        <p class="text-sm text-orange-600 mt-1">⚠️ Changing your WhatsApp number costs 100 credits</p>
                    </div>

                    <!-- Step 1: Enter New Number and Password -->
                    <div x-show="step === 1" x-transition>
                        <form wire:submit.prevent="initiateNumberChange" class="space-y-4">
                            <div>
                                <label for="newWhatsappNumber" class="block text-sm font-medium text-gray-700 mb-2">New WhatsApp Number</label>
                                <input type="text" id="newWhatsappNumber" wire:model="newWhatsappNumber" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                       placeholder="+234XXXXXXXXXX">
                                @error('newWhatsappNumber') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                <input type="password" id="password" wire:model="password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                       placeholder="Enter your password">
                                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50"
                                    wire:loading.attr="disabled" wire:target="initiateNumberChange">
                                <span wire:loading.remove wire:target="initiateNumberChange">Send OTP</span>
                                <span wire:loading wire:target="initiateNumberChange">Sending OTP...</span>
                            </button>
                        </form>
                    </div>

                    <!-- Step 2: Verify OTP -->
                    <div x-show="step === 2" x-transition>
                        <form wire:submit.prevent="verifyNumberChange" class="space-y-4">
                            <div>
                                <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">Enter OTP</label>
                                <input type="text" id="otp" wire:model="otp" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-center text-lg tracking-widest"
                                       placeholder="000000" maxlength="6">
                                @error('otp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-sm text-gray-600">OTP sent to {{ $newWhatsappNumber }}</p>
                                <p class="text-sm text-gray-500">Attempts: {{ $otpAttempts }}/{{ $maxOtpAttempts }}</p>
                            </div>

                            <div class="flex space-x-3">
                                <button type="submit" 
                                        class="flex-1 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50"
                                        wire:loading.attr="disabled" wire:target="verifyNumberChange">
                                    <span wire:loading.remove wire:target="verifyNumberChange">Verify OTP</span>
                                    <span wire:loading wire:target="verifyNumberChange">Verifying...</span>
                                </button>
                                <button type="button" wire:click="resetNumberChangeForm" 
                                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors"
                                        @click="step = 1">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Google People API -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Google Contacts Integration</h2>
                    
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-blue-500 mr-3" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900">Google Contacts</h3>
                                <p class="text-sm text-gray-600">
                                    @if($googleConnected)
                                        Connected - Sync your Google contacts
                                    @else
                                        Connect to sync your Google contacts
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        @if($googleConnected)
                            <button wire:click="disconnectGoogle" 
                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors">
                                Disconnect
                            </button>
                        @else
                            <button wire:click="connectGoogle" 
                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50"
                                    wire:loading.attr="disabled" wire:target="connectGoogle">
                                <span wire:loading.remove wire:target="connectGoogle">Connect Google</span>
                                <span wire:loading wire:target="connectGoogle">Connecting...</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Notification Preferences -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Notification Preferences</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-green-500 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                                <div>
                                    <h3 class="font-medium text-gray-900">WhatsApp Notifications</h3>
                                    <p class="text-sm text-gray-600">Receive notifications via WhatsApp</p>
                                </div>
                            </div>
                            
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="notifyWhatsapp" wire:change="toggleNotifications" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>