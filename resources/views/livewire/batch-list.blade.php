<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <!-- Banner Section -->
    @if ($this->settingService->isBannerEnabled())
        <div class="p-3">
            <div class="banner-container  mb-6 overflow-hidden rounded-2xl shadow-sm" x-data="bannerSlider()">
                <div class="relative   ">
                    <!-- Guest User Banner -->
                    @guest
                        <div class="banner-slide p-3 active"
                            :class="{ 'gradient-bg': bannerType === 'gradient', 'image-bg': bannerType === 'image', 'color-bg': bannerType === 'color' }"
                            style="background: linear-gradient(135deg, #e16010 0%, #764ba2 100%);">
                            @if (
                                $this->settingService->get('banner_guest_background_type') === 'image' &&
                                    $this->settingService->get('banner_guest_background_image'))
                                <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
                                    style="background-image: url('{{ Storage::url($this->settingService->get('banner_guest_background_image')) }}');">
                                </div>
                                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                            @endif

                            <div class="relative z-10 h-full flex items-center justify-center px-6 md:px-12">
                                <div class="text-center text-white max-w-4xl mx-auto">
                                    <h1 class="text-2xl md:text-5xl lg:text-6xl font-bold mb-4 animate-fade-in-up"
                                        x-data="typewriter('{{ $this->settingService->get('banner_guest_title', 'Welcome to Yapa') }}')" x-text="displayText"></h1>

                                    <p class="text-xl md:text-2xl lg:text-3xl mb-6 animate-fade-in-up animation-delay-300 opacity-90"
                                        x-data="typewriter('{{ $this->settingService->get('banner_guest_subtitle', 'Connect, Network, Grow Together') }}', 1000)" x-text="displayText"></p>

                                    <p
                                        class="text-lg md:text-xl mb-8 animate-fade-in-up animation-delay-600 opacity-80 max-w-2xl mx-auto">
                                        {{ $this->settingService->get('banner_guest_description', 'Join our vibrant community and discover meaningful connections. Network with like-minded individuals and grow your professional circle.') }}
                                    </p>

                                    <div
                                        class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up animation-delay-900">
                                        <a href="{{ $this->settingService->get('banner_guest_button_url', '/register') }}"
                                            class="bg-white text-purple-600 px-5 py-2 rounded-full font-semibold text-sm hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                                            {{ $this->settingService->get('banner_guest_button_text', 'Get Started') }}
                                        </a>
                                        <a href="{{ $this->settingService->get('banner_guest_secondary_button_url', '/login') }}"
                                            class="border-2 border-white text-white px-5 py-2 rounded-full font-semibold text-sm hover:bg-white hover:text-purple-600 transform hover:scale-105 transition-all duration-300">
                                            {{ $this->settingService->get('banner_guest_secondary_button_text', 'Login') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Animated Background Elements -->
                            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                                <div class="floating-shape shape-1"></div>
                                <div class="floating-shape shape-2"></div>
                                <div class="floating-shape shape-3"></div>
                            </div>
                        </div>
                    @endguest

                    <!-- Authenticated User Banner -->
                    @auth
                        <div class="banner-slide active"
                            style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);">
                            @if (
                                $this->settingService->get('banner_auth_background_type') === 'image' &&
                                    $this->settingService->get('banner_auth_background_image'))
                                <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
                                    style="background-image: url('{{ Storage::url($this->settingService->get('banner_auth_background_image')) }}');">
                                </div>
                                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                            @endif

                            <div class="relative z-10 h-full flex items-center justify-center px-6 md:px-12">
                                <div class="text-cente text-white max-w-4xl mx-auto">
                                    <div class="flex items-center p-2 gap-3 justify-between">
                                        <div class="">
                                            <div class="bg-white bg-opacity-20 rounded-full animate-pulse-slow">
                                                <svg class="w-6 h-6 md:w-20 md:h-20 text-white" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="">
                                            <div class="mb-1">
                                                <h1 class="text-sm md:text-2xl lg:text-3xl font-bold  animate-fade-in-up"
                                                    x-data="typewriter('{{ $this->settingService->get('banner_auth_title', 'Join Our WhatsApp Community') }}')" x-text="displayText"></h1>

                                                <p class="text-xs md:text-xl lg:text-2xl animate-fade-in-up animation-delay-300 opacity-90"
                                                    x-data="typewriter('{{ $this->settingService->get('banner_auth_subtitle', 'Stay Connected & Get Updates') }}', 1000)" x-text="displayText"></p>


                                            </div>
                                            <div class="animate-fade-in-up animation-delay-900">
                                                <a href="{{ $this->settingService->get('banner_auth_button_url', 'https://chat.whatsapp.com/your-group-link') }}"
                                                    target="_blank"
                                                    class="bg-white text-green-600 px-5 py-2 rounded-full font-semibold text-xs hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg inline-flex items-center gap-3">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                                                    </svg>
                                                    {{ $this->settingService->get('banner_auth_button_text', 'Join WhatsApp Channel') }}
                                                </a>
                                            </div>
                                            
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <!-- Animated Background Elements -->
                            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                                <div class="floating-shape shape-1 bg-white bg-opacity-10"></div>
                                <div class="floating-shape shape-2 bg-white bg-opacity-10"></div>
                                <div class="floating-shape shape-3 bg-white bg-opacity-10"></div>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    @endif
    <!-- Filter Button -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2 text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
                <span class="text-sm font-medium">{{ $batches->count() }} batches available</span>
            </div>

            <button onclick="openFilterModal()"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4">
                    </path>
                </svg>
                <span class="text-sm font-medium">Filters</span>
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 animate-fade-in">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"></path>
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        @if ($batches->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                @foreach ($batches as $batch)
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">
                        <!-- Batch Header -->
                        <div class="p-4 sm:p-5 flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate">
                                        {{ $batch->name }}</h3>

                                    @if ($batch->type === 'trial')
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-green-100 to-green-200 text-green-800 mt-2">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Trial
                                        </span>
                                    @endif
                                </div>

                                <div class="text-right ml-2">
                                    <div class="text-xs text-gray-500">Members</div>
                                    <div
                                        class="text-lg font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">
                                        {{ $batch->getCurrentMemberCount() }}/{{ $batch->limit }}
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
                                <div class="bg-gradient-to-r from-orange-500 to-purple-500 h-2 rounded-full transition-all duration-500 ease-out"
                                    style="width: {{ $batch->getFillPercentage() }}%"></div>
                            </div>

                            <!-- Batch Details -->
                            <div class="space-y-2">
                                <!-- Location and Interests on same line -->
                                @if ($batch->location || $batch->interests->count() > 0)
                                    <div class="flex items-center text-xs sm:text-sm text-gray-600">
                                        @if ($batch->location)
                                            <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="truncate">{{ $batch->location }}</span>
                                        @endif
                                        @if ($batch->location && $batch->interests->count() > 0)
                                            <span class="mx-2 text-gray-400">•</span>
                                        @endif
                                        @if ($batch->interests->count() > 0)
                                            <div class="flex flex-wrap gap-1 items-center min-w-0">
                                                @foreach ($batch->interests->take(2) as $interest)
                                                    <span
                                                        class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 truncate">
                                                        {{ $interest->name }}
                                                    </span>
                                                @endforeach
                                                @if ($batch->interests->count() > 2)
                                                    <span
                                                        class="text-xs text-gray-500">+{{ $batch->interests->count() - 2 }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="flex items-center text-xs sm:text-sm">
                                    @if ($batch->type === 'regular')
                                        <svg class="w-4 h-4 mr-2 text-orange-500" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z">
                                            </path>
                                        </svg>
                                        <span
                                            class="font-medium text-orange-600">{{ number_format($batch->cost_in_credits) }}
                                            credits</span>
                                    @else
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium text-green-600">Free Trial</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Batch Actions -->
                        {{-- ======================= CORRECTED BLOCK STARTS HERE ======================= --}}
                        <div class="px-4 sm:px-5 py-3 bg-gray-50 border-t border-gray-100 mt-auto"
                            x-data="{ showShareMenu: false }">
                            @if(Auth::check())
                                @php
                                    $user = Auth::user();
                                    $isMember = $batch->members()->where('user_id', $user->id)->exists();
                                    $canJoin = $batch->canUserJoin($user);
                                    $isFull = $batch->isFull();
                                    $canShare = $batch->isOpen() && !$batch->isFull();
                                @endphp

                                @if ($isMember)
                                @if ($isFull)
                                    {{-- Case 1: User is a member and the batch is FULL --}}
                                    <div class="flex gap-2">
                                        <button wire:click="downloadVcf({{ $batch->id }})"
                                            class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            Download VCF
                                        </button>
                                    </div>
                                @else
                                    {{-- Case 2: User is a member and the batch is WAITING (not full) --}}
                                    <div class="flex gap-2">
                                        <div
                                            class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-xl text-gray-600 bg-gray-100 cursor-default">
                                            <div class="w-4 h-4 rounded-full bg-orange-500 animate-pulse mr-2"></div>
                                            Waiting...
                                        </div>
                                        @if ($canShare)
                                            <div class="relative">
                                                <button @click="showShareMenu = !showShareMenu"
                                                    class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-xl text-orange-600 bg-orange-50 border border-orange-200 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <!-- Share Menu -->
                                                <div x-show="showShareMenu" @click.away="showShareMenu = false"
                                                    x-transition
                                                    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                                    <div
                                                        class="bg-white rounded-xl shadow-lg border border-gray-200 p-4 max-w-sm w-full mx-4">
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <button
                                                                @click="shareOnWhatsApp({{ $batch->id }}, '{{ $batch->name }}')"
                                                                class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-green-600 bg-green-50 hover:bg-green-100 transition-colors">
                                                                <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785" />
                                                                </svg> WhatsApp
                                                            </button>
                                                            <button
                                                                @click="shareOnFacebook({{ $batch->id }}, '{{ $batch->name }}')"
                                                                class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                                                <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                                                </svg> Facebook
                                                            </button>
                                                            <button
                                                                @click="shareOnTwitter({{ $batch->id }}, '{{ $batch->name }}')"
                                                                class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-gray-800 bg-gray-100 hover:bg-gray-200 transition-colors">
                                                                <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                                                </svg> X (Twitter)
                                                            </button>
                                                            <button
                                                                @click="copyShareLink({{ $batch->id }}, '{{ $batch->name }}')"
                                                                class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-purple-600 bg-purple-50 hover:bg-purple-100 transition-colors">
                                                                <svg class="w-4 h-4 mr-1" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                                    </path>
                                                                </svg> Copy Link
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @else
                                {{-- Case 3: User is NOT a member --}}
                                <div class="flex gap-2">
                                    @if ($canJoin)
                                        <button wire:click="joinBatch({{ $batch->id }})"
                                            @if ($isProcessing ?? false) disabled @endif
                                            class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105">
                                            @if ($isProcessing ?? false)
                                                <svg class="animate-spin h-4 w-4 text-white" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            @else
                                                Join Batch
                                            @endif
                                        </button>
                                    @else
                                        <div class="flex-1 relative">
                                            <button disabled
                                                class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-xl text-gray-500 bg-gray-100 cursor-not-allowed">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728">
                                                    </path>
                                                </svg>
                                                Unavailable
                                            </button>
                                        </div>
                                    @endif

                                    @if ($canShare)
                                        <div class="relative">
                                            <button @click="showShareMenu = !showShareMenu"
                                                class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-xl text-orange-600 bg-orange-50 border border-orange-200 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <!-- Share Menu -->
                                            <div x-show="showShareMenu" @click.away="showShareMenu = false"
                                                x-transition
                                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                                <div
                                                    class="bg-white rounded-xl shadow-lg border border-gray-200 p-4 max-w-sm w-full mx-4">
                                                    <div class="text-xs text-gray-600 text-center mb-2 px-2">
                                                        Earn 100 credits when 10 people join!
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <button
                                                            @click="shareOnWhatsApp({{ $batch->id }}, '{{ $batch->name }}')"
                                                            class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-green-600 bg-green-50 hover:bg-green-100 transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785" />
                                                            </svg> WhatsApp
                                                        </button>
                                                        <button
                                                            @click="shareOnFacebook({{ $batch->id }}, '{{ $batch->name }}')"
                                                            class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                                            </svg> Facebook
                                                        </button>
                                                        <button
                                                            @click="shareOnTwitter({{ $batch->id }}, '{{ $batch->name }}')"
                                                            class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-gray-800 bg-gray-100 hover:bg-gray-200 transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                                            </svg> X (Twitter)
                                                        </button>
                                                        <button
                                                            @click="copyShareLink({{ $batch->id }}, '{{ $batch->name }}')"
                                                            class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-purple-600 bg-purple-50 hover:bg-purple-100 transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                                </path>
                                                            </svg> Copy Link
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @else
                                {{-- Guest User Section --}}
                                @php
                                    $isFull = $batch->isFull();
                                    $canShare = $batch->isOpen() && !$batch->isFull();
                                @endphp
                                
                                <div class="flex gap-2">
                                    <div class="flex-1 relative">
                                        <a href="{{ route('login') }}"
                                            class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                            </svg>
                                            Login to Join
                                        </a>
                                    </div>
                                    
                                    @if ($canShare)
                                        <div class="relative">
                                            <button @click="showShareMenu = !showShareMenu"
                                                class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-xl text-orange-600 bg-orange-50 border border-orange-200 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                                </svg>
                                            </button>
                                            <!-- Share Menu for Guests -->
                                            <div x-show="showShareMenu" @click.away="showShareMenu = false" x-transition
                                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-4 max-w-sm w-full mx-4">
                                                    <div class="text-xs text-gray-600 text-center mb-2 px-2">
                                                        Share this batch with friends!
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <button @click="shareOnWhatsApp({{ $batch->id }}, '{{ $batch->name }}')"
                                                            class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-green-600 bg-green-50 hover:bg-green-100 transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785" />
                                                            </svg> WhatsApp
                                                        </button>
                                                        <button @click="shareOnFacebook({{ $batch->id }}, '{{ $batch->name }}')"
                                                            class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                                            </svg> Facebook
                                                        </button>
                                                        <button @click="shareOnTwitter({{ $batch->id }}, '{{ $batch->name }}')"
                                                            class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-gray-800 bg-gray-100 hover:bg-gray-200 transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                                            </svg> X (Twitter)
                                                        </button>
                                                        <button @click="copyShareLink({{ $batch->id }}, '{{ $batch->name }}')"
                                                            class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-purple-600 bg-purple-50 hover:bg-purple-100 transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                            </svg> Copy Link
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        {{-- ======================== CORRECTED BLOCK ENDS HERE ======================== --}}
                    </div>
                @endforeach
            </div>

            <!-- Load More Button -->
            @if($hasMorePages)
                <div class="mt-8 text-center">
                    <button wire:click="loadMore" 
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-purple-500 text-white font-medium rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Load More Batches
                    </button>
                </div>
            @endif
            
            <!-- Auto-scroll trigger -->
            <div id="scroll-trigger" class="h-1"></div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div
                    class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-orange-100 to-purple-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No batches found</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    @if (array_filter($filters))
                        Try adjusting your filters to discover more batches that match your interests.
                    @else
                        There are no available batches at the moment. Check back later for new opportunities!
                    @endif
                </p>
                @if (array_filter($filters))
                    <button wire:click="clearFilters"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear Filters
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Buy Credits Floating Button -->
    <div class="fixed bottom-6 right-6 z-40">
        <a href="{{ route('credits.purchase') }}"
            class="inline-flex items-center px-6 py-3 text-sm font-medium rounded-2xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z">
                </path>
            </svg>
            Buy Credits
        </a>
    </div>

    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeFilterModal()">
            </div>

            <div
                class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4">
                            </path>
                        </svg>
                        Filter Batches
                    </h3>
                    <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Location Filter -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Location
                        </label>
                        <select wire:model.live="filters.location" id="location"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                            <option value="">All Locations</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                </path>
                            </svg>
                            Batch Type
                        </label>
                        <select wire:model.live="filters.type" id="type"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors duration-200">
                            <option value="all">All Types</option>
                            <option value="trial">Trial Batches</option>
                            <option value="regular">Regular Batches</option>
                        </select>
                    </div>

                    <!-- Interests Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                            Interests
                        </label>
                        <div
                            class="max-h-40 overflow-y-auto border border-gray-300 rounded-xl p-3 bg-gray-50 space-y-2">
                            @foreach ($interests as $interest)
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model.live="filters.interests"
                                        value="{{ $interest->id }}"
                                        class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $interest->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3 mt-8">
                    <button wire:click="clearFilters" onclick="closeFilterModal()"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear All
                    </button>
                    <button onclick="closeFilterModal()"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-purple-500 border border-transparent rounded-xl hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Apply Filters
                    </button>
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

        /* Custom scrollbar for interests */
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
    </style>

    <script>
        function openFilterModal() {
            document.getElementById('filterModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            const modal = document.querySelector('#filterModal > div > div');
            modal.style.transform = 'scale(0.95)';
            modal.style.opacity = '0';

            setTimeout(() => {
                modal.style.transform = 'scale(1)';
                modal.style.opacity = '1';
                modal.style.transition = 'all 0.2s ease-out';
            }, 10);
        }

        function closeFilterModal() {
            const modal = document.querySelector('#filterModal > div > div');
            modal.style.transform = 'scale(0.95)';
            modal.style.opacity = '0';
            modal.style.transition = 'all 0.15s ease-in';

            setTimeout(() => {
                document.getElementById('filterModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 150);
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeFilterModal();
            }
        });

        document.documentElement.style.scrollBehavior = 'smooth';

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

        document.addEventListener('DOMContentLoaded', function() {
            const batchCards = document.querySelectorAll('.grid > div');
            batchCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition =
                    `opacity 0.6s ease-out ${index * 0.1}s, transform 0.6s ease-out ${index * 0.1}s`;
                observer.observe(card);
            });
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('batch-joined', (event) => {
                showNotification(event.message || 'Successfully joined the batch!', 'success');
            });

            Livewire.on('batch-join-error', (event) => {
                showNotification(event.message || 'Failed to join the batch!', 'error');
            });

            Livewire.on('batchShared', (event) => {
                showNotification(event.message || 'Batch shared successfully!', 'success');
            });
        });

        // Alpine.js sharing functions
        function shareOnWhatsApp(batchId, batchName) {
            const shareUrl = generateShareUrl(batchId);
            const message = `Check out this amazing batch: ${batchName}\n\nJoin now: ${shareUrl}`;
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;

            @this.call('shareBatch', batchId, 'whatsapp');
            window.open(whatsappUrl, '_blank');
        }

        function shareOnFacebook(batchId, batchName) {
            const shareUrl = generateShareUrl(batchId);
            const facebookUrl =
                `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}"e=${encodeURIComponent(`Join this amazing batch: ${batchName}`)}`;

            @this.call('shareBatch', batchId, 'facebook');
            window.open(facebookUrl, '_blank', 'width=600,height=400');
        }

        function shareOnTwitter(batchId, batchName) {
            const shareUrl = generateShareUrl(batchId);
            const tweetText = `Check out this amazing batch: ${batchName}`;
            const twitterUrl =
                `https://twitter.com/intent/tweet?text=${encodeURIComponent(tweetText)}&url=${encodeURIComponent(shareUrl)}`;

            @this.call('shareBatch', batchId, 'twitter');
            window.open(twitterUrl, '_blank', 'width=600,height=400');
        }

        function copyShareLink(batchId, batchName) {
            const shareUrl = generateShareUrl(batchId);

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(shareUrl).then(() => {
                    showNotification('Link copied to clipboard!', 'success');
                }).catch(() => {
                    fallbackCopyTextToClipboard(shareUrl);
                });
            } else {
                fallbackCopyTextToClipboard(shareUrl);
            }

            @this.call('shareBatch', batchId, 'copy_link');
        }

        function generateShareUrl(batchId) {
            const baseUrl = window.location.origin;
            const referralCode = '{{ auth()->user()->referral_code ?? '' }}';
            return `${baseUrl}/batch/${batchId}?ref=${referralCode}`;
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
                const successful = document.execCommand('copy');
                if (successful) {
                    showNotification('Link copied to clipboard!', 'success');
                } else {
                    showNotification('Failed to copy link', 'error');
                }
            } catch (err) {
                showNotification('Failed to copy link', 'error');
            }

            document.body.removeChild(textArea);
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            notification.className =
                `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full opacity-0`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 10);

            setTimeout(() => {
                notification.style.transform = 'translateX(120%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Banner functionality
        function bannerSlider() {
            return {
                currentSlide: 0,
                autoSlideEnabled: @json($this->settingService->get('banner_auto_slide', true)),
                slideInterval: @json($this->settingService->get('banner_slide_interval', 5)) * 1000,

                init() {
                    if (this.autoSlideEnabled) {
                        this.startAutoSlide();
                    }
                },

                startAutoSlide() {
                    setInterval(() => {
                        this.nextSlide();
                    }, this.slideInterval);
                },

                nextSlide() {
                    const slides = this.$el.querySelectorAll('.banner-slide');
                    if (slides.length > 1) {
                        slides[this.currentSlide].classList.remove('active');
                        this.currentSlide = (this.currentSlide + 1) % slides.length;
                        slides[this.currentSlide].classList.add('active');
                    }
                }
            }
        }

        function typewriter(text, delay = 0) {
            return {
                displayText: '',
                fullText: text,
                currentIndex: 0,

                init() {
                    setTimeout(() => {
                        this.startTyping();
                    }, delay);
                },

                startTyping() {
                    const interval = setInterval(() => {
                        if (this.currentIndex < this.fullText.length) {
                            this.displayText += this.fullText[this.currentIndex];
                            this.currentIndex++;
                        } else {
                            clearInterval(interval);
                        }
                    }, 50);
                }
            }
        }
    </script>

    <style>
        /* Banner Animations */
        .banner-slide {
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .banner-slide.active {
            opacity: 1;
            transform: translateX(0);
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
            opacity: 0;
        }

        .animation-delay-300 {
            animation-delay: 0.3s;
        }

        .animation-delay-600 {
            animation-delay: 0.6s;
        }

        .animation-delay-900 {
            animation-delay: 0.9s;
        }

        @keyframes pulse-slow {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }

        /* Floating Shapes */
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .shape-1 {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .shape-3 {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            33% {
                transform: translateY(-20px) rotate(120deg);
            }

            66% {
                transform: translateY(10px) rotate(240deg);
            }
        }







        /* Button Hover Effects */
        .banner-slide a {
            position: relative;
            overflow: hidden;
        }

        .banner-slide a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .banner-slide a:hover::before {
            left: 100%;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollTrigger = document.getElementById('scroll-trigger');
            
            if (scrollTrigger) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            @this.call('loadMore');
                        }
                    });
                }, {
                    rootMargin: '100px'
                });
                
                observer.observe(scrollTrigger);
            }
        });
    </script>
</div>
