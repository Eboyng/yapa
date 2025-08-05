<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="container mx-auto px-4 py-6 max-w-2xl">
        @if ($adTask)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
               
                <div class="p-6">
                    <!-- Ad Content -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $ad->title }}</h2>

                        @if ($ad->banner)
                            <div class="mb-6">
                                <div class="relative group rounded-2xl overflow-hidden">
                                    <img src="{{ Storage::url($ad->banner) }}" alt="{{ $ad->title }}"
                                        class="w-full h-48 object-cover cursor-pointer transition-transform duration-300 group-hover:scale-105"
                                        onclick="window.open('{{ $ad->url }}', '_blank')">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300 cursor-pointer flex items-center justify-center"
                                        onclick="window.open('{{ $ad->url }}', '_blank')">
                                        <div
                                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white/90 rounded-full p-3">
                                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <p class="text-gray-600 mb-6 leading-relaxed">{{ $ad->description }}</p>

                        <!-- Copy Content Section -->
                        <div class="bg-gradient-to-r from-orange-50 to-purple-50 rounded-2xl p-6 mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Content to Share
                                </h3>
                                <button onclick="copyContent()" id="copyButton"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-purple-500 text-white text-sm font-medium rounded-xl hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                                    <svg id="copyIcon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <svg id="checkIcon" class="w-4 h-4 mr-2 hidden" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span id="copyText">Copy</span>
                                </button>
                            </div>
                            <div
                                class="text-sm text-gray-700 whitespace-pre-line border border-gray-200 rounded-xl p-4 bg-white shadow-sm">
                                {{ $ad->title }}

                                {{ $ad->description }}

                                {{ $ad->url }}
                            </div>
                        </div>
                    </div>

                    <!-- Task Instructions -->
                    <div class="mb-8">
                        @if ($adTask->status === 'active')
                            <div
                                class="bg-gradient-to-r mb-3 from-blue-50 to-blue-100 border border-blue-200 rounded-2xl p-6">
                                <div class="flex items-start">
                                    <div
                                        class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 mb-3">
                                        <h3 class="font-semibold text-blue-900 mb-3">Next Steps:</h3>
                                        <ol class="text-sm text-blue-800 space-y-2">
                                            <li class="flex items-start">
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white text-xs font-medium rounded-full mr-3 mt-0.5 flex-shrink-0">1</span>
                                                <span>Copy the content above using the copy button</span>
                                            </li>
                                            <li class="flex items-start">
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white text-xs font-medium rounded-full mr-3 mt-0.5 flex-shrink-0">2</span>
                                                <span>Share it as your WhatsApp Status</span>
                                            </li>
                                            <li class="flex items-start">
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white text-xs font-medium rounded-full mr-3 mt-0.5 flex-shrink-0">3</span>
                                                <span>Submit your screenshot and view count within 48 hours</span>
                                            </li>
                                        </ol>
                                        <div class="mt-4 p-3 bg-blue-100 rounded-xl">
                                            <p class="text-xs text-blue-700 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Task started: {{ $adTask->created_at->format('M j, Y g:i A') }}
                                            </p>
                                            @if($timeRemaining)
                                                <p class="text-xs text-blue-700 flex items-center mt-1">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Time remaining: {{ $hoursRemaining }} hours
                                                </p>
                                                <p class="text-xs text-blue-700 flex items-center mt-1">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Deadline: {{ $timeRemaining->format('M j, Y g:i A') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($adTask->status === 'active' && $hoursRemaining <= 6 && $hoursRemaining > 0)
                            <!-- Expiry Warning -->
                            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-2xl p-6 mb-6">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-yellow-500 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-yellow-900 mb-2">⚠️ Task Expiring Soon!</h3>
                                        <p class="text-sm text-yellow-800 mb-3">
                                            You have only <strong>{{ $hoursRemaining }} hours</strong> left to submit your screenshot. Please complete your task soon to avoid automatic rejection.
                                        </p>
                                        <div class="bg-yellow-100 rounded-xl p-3">
                                            <p class="text-xs text-yellow-700 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Deadline: {{ $timeRemaining->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($adTask->status === 'rejected' && str_contains($adTask->rejection_reason, 'expired'))
                            <!-- Task Expired Message -->
                            <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-2xl p-6 mb-6">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-red-900 mb-2">Task Expired</h3>
                                        <p class="text-sm text-red-800 mb-3">
                                            This task has expired because you did not submit your screenshot within 48 hours. The task has been automatically rejected.
                                        </p>
                                        <div class="bg-red-100 rounded-xl p-3">
                                            <p class="text-xs text-red-700 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Task started: {{ $adTask->created_at->format('M j, Y g:i A') }}
                                            </p>
                                            <p class="text-xs text-red-700 flex items-center mt-1">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Expired: {{ $adTask->created_at->addHours(48)->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($adTask->status === 'active' && $canUploadScreenshot)
                            <!-- Submission Form -->
                            <div class="bg-white border border-gray-200 rounded-2xl p-6 space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Submit Your Results
                                </h3>

                                <!-- Screenshot Upload -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Screenshot of WhatsApp Status
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" wire:model="screenshot" accept="image/jpeg,image/png"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-gradient-to-r file:from-orange-50 file:to-purple-50 file:text-gray-700 hover:file:from-orange-100 hover:file:to-purple-100 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">
                                    </div>
                                    @error('screenshot')
                                        <p class="text-red-500 text-xs mt-2 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                </path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="text-xs text-gray-500 mt-2 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Max size: 2MB. Formats: JPG, PNG only.
                                    </p>
                                </div>

                                <!-- View Count Input -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        View Count
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" wire:model="viewCount" min="1"
                                        placeholder="Enter the number of views"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">
                                    @error('viewCount')
                                        <p class="text-red-500 text-xs mt-2 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                </path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="text-xs text-gray-500 mt-2 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Enter the exact view count shown in your screenshot.
                                    </p>
                                </div>

                                <!-- Estimated Earnings -->
                                @if ($viewCount && is_numeric($viewCount))
                                    <div
                                        class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl p-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-green-800">Estimated Earnings</p>
                                                <p class="text-lg font-bold text-green-600">
                                                    ₦{{ number_format($viewCount * ($adSettings['share_per_view_rate'] ?? 0), 2) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Submit Button -->
                                <button wire:click="submitTask" wire:loading.attr="disabled"
                                    class="w-full flex items-center justify-center py-4 px-6 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-700 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 disabled:hover:scale-100 shadow-lg hover:shadow-xl">
                                    <span wire:loading.remove class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Submit Task
                                    </span>
                                    <span wire:loading class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Submitting...
                                    </span>
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Success/Error Messages -->
                    @if (session()->has('message'))
                        <div
                            class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl p-4 animate-fade-in">
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-green-800 font-medium">{{ session('message') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div
                            class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-xl p-4 animate-fade-in">
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-red-800 font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Navigation -->
                    <div class="flex flex-col sm:flex-row justify-between gap-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('ads.index') }}"
                            class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Ads
                        </a>

                        <a href="{{ route('ads.tasks') }}"
                            class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-500 to-purple-500 text-white text-sm font-medium rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Task History
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div
                    class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-orange-100 to-purple-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Active Task</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    You don't have any active ad task at the moment. Browse available campaigns to start earning.
                </p>
                <a href="{{ route('ads.index') }}"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Browse Available Ads
                </a>
            </div>
        @endif
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

        /* Custom file input styling */
        input[type="file"]::-webkit-file-upload-button {
            transition: all 0.2s ease-out;
        }

        /* Custom number input styling */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Smooth transitions for form elements */
        input:focus {
            transform: scale(1.01);
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
    </style>

    <script>
        // Copy content functionality
        function copyContent() {
            const content = `{{ addslashes($ad->title . "\n\n" . $ad->description . "\n\n" . $ad->url) }}`;
            const copyButton = document.getElementById('copyButton');
            const copyIcon = document.getElementById('copyIcon');
            const checkIcon = document.getElementById('checkIcon');
            const copyText = document.getElementById('copyText');

            navigator.clipboard.writeText(content).then(() => {
                // Show success state
                copyIcon.classList.add('hidden');
                checkIcon.classList.remove('hidden');
                copyText.textContent = 'Copied!';
                copyButton.classList.add('bg-green-500', 'hover:bg-green-600');
                copyButton.classList.remove('from-orange-500', 'to-purple-500', 'hover:from-orange-600',
                    'hover:to-purple-600');

                // Reset after 2 seconds
                setTimeout(() => {
                    copyIcon.classList.remove('hidden');
                    checkIcon.classList.add('hidden');
                    copyText.textContent = 'Copy';
                    copyButton.classList.remove('bg-green-500', 'hover:bg-green-600');
                    copyButton.classList.add('from-orange-500', 'to-purple-500', 'hover:from-orange-600',
                        'hover:to-purple-600');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

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

        // Observe elements for scroll animations
        document.addEventListener('DOMContentLoaded', function() {
            const animatedElements = document.querySelectorAll('.bg-white, .bg-gradient-to-r');
            animatedElements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                element.style.transition =
                    `opacity 0.6s ease-out ${index * 0.1}s, transform 0.6s ease-out ${index * 0.1}s`;
                observer.observe(element);
            });
        });

        // Add focus animations for form inputs
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="file"], input[type="number"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.01)';
                    this.parentElement.style.transition = 'transform 0.2s ease-out';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
        });

        // Add click ripple effect for buttons
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('button:not([disabled]), a[class*="bg-gradient"]');
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
        });

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }
        
        .status-indicator {
            animation: pulse 2s ease-in-out infinite;
        }
    `;
        document.head.appendChild(style);

        // Add status indicator animations
        document.addEventListener('DOMContentLoaded', function() {
            const statusBadges = document.querySelectorAll('[class*="bg-green-500"], [class*="bg-yellow-500"]');
            statusBadges.forEach(badge => {
                if (badge.textContent.includes('Active') || badge.textContent.includes('Under Review')) {
                    badge.classList.add('status-indicator');
                }
            });
        });

        // Add image hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img[onclick]');
            images.forEach(img => {
                img.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05)';
                });

                img.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });

        // Add step number animations
        document.addEventListener('DOMContentLoaded', function() {
            const stepNumbers = document.querySelectorAll('.bg-blue-500.text-white.rounded-full');
            stepNumbers.forEach((step, index) => {
                setTimeout(() => {
                    step.style.transform = 'scale(1.1)';
                    step.style.transition = 'transform 0.3s ease-out';
                    setTimeout(() => {
                        step.style.transform = 'scale(1)';
                    }, 300);
                }, index * 200);
            });
        });
    </script>
</div>
