<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="bg-white shadow-lg border-b border-orange-100 rounded-2xl p-4 sm:p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-3 mb-4 sm:mb-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-orange-500 to-purple-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h1
                            class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">
                            Share & Earn
                        </h1>
                        <p class="text-sm sm:text-base text-gray-600 mt-1">
                            Complete tasks and earn money by sharing content
                        </p>
                    </div>
                </div>

                <button onclick="openGuideModal()"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-purple-500 text-white text-sm font-medium rounded-xl hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    How it Works
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-xs text-gray-600 truncate">Per View</p>
                        <p class="text-sm font-bold text-green-600">
                            ₦{{ number_format($adSettings['share_per_view_rate'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-xs text-gray-600 truncate">Available Ads</p>
                        <p class="text-sm font-bold text-blue-600">{{ $ads->count() }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-xs text-gray-600 truncate">Active Users</p>
                        <p class="text-sm font-bold text-purple-600">{{ $ads->sum('participants_count') }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-orange-100 to-orange-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-xs text-gray-600 truncate">Status</p>
                        <p class="text-sm font-bold {{ $hasActiveTask ? 'text-orange-600' : 'text-gray-600' }}">
                            {{ $hasActiveTask ? 'Active' : 'Ready' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ad Cards -->
        @if ($ads->count() > 0)
            <div class="grid gap-4 sm:gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($ads as $ad)
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1">
                        {{-- <!-- Banner Image -->
                        @if ($ad->banner)
                            <div
                                class="relative aspect-video bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                <img src="{{ Storage::url($ad->banner) }}" alt="{{ $ad->title }}"
                                    class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300"
                                    onclick="window.open('{{ $ad->url }}', '_blank')">
                                <div class="absolute inset-0 bg-black/0 hover:bg-black/10 transition-colors duration-300 cursor-pointer"
                                    onclick="window.open('{{ $ad->url }}', '_blank')"></div>
                            </div>
                        @else
                            <div
                                class="aspect-video bg-gradient-to-br from-orange-100 to-purple-100 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        @endif --}}

                        <div class="p-4">
                            <!-- Title -->
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                {{ $ad->title }}</h3>

                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($ad->description, 120) }}
                            </p>

                            <!-- Earnings Info -->
                            <div class="flex items-center justify-between mb-4 p-3 bg-gray-50 rounded-xl">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                        </path>
                                    </svg>
                                    <span
                                        class="text-sm font-semibold text-green-600">₦{{ number_format($adSettings['share_per_view_rate'] ?? 0, 2) }}</span>
                                    <span class="text-xs text-gray-500 ml-1">per view</span>
                                </div>
                                <div class="text-xs text-gray-500 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                        </path>
                                    </svg>
                                    {{ $ad->participants_count }}/{{ $ad->max_participants }}
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                                <div class="bg-gradient-to-r from-orange-500 to-purple-500 h-2 rounded-full transition-all duration-500"
                                    style="width: {{ ($ad->participants_count / $ad->max_participants) * 100 }}%">
                                </div>
                            </div>

                            <!-- Earn Button -->
                            @php
                                $isDisabled =
                                    $hasActiveTask ||
                                    $ad->participants_count >= $ad->max_participants ||
                                    auth()->user()->isFlaggedForAds();
                                $buttonText = '';
                                $buttonIcon = '';

                                if (auth()->user()->isFlaggedForAds()) {
                                    $buttonText = 'Account Flagged';
                                    $buttonIcon =
                                        'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728';
                                } elseif ($hasActiveTask) {
                                    $buttonText = 'Task in Progress';
                                    $buttonIcon = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
                                } elseif ($ad->participants_count >= $ad->max_participants) {
                                    $buttonText = 'Completed';
                                    $buttonIcon = 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
                                } else {
                                    $buttonText = 'Start Earning';
                                    $buttonIcon =
                                        'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1';
                                }
                            @endphp

                            <button wire:click="startAdTask({{ $ad->id }})"
                                @if ($isDisabled) disabled @endif
                                class="w-full flex items-center justify-center px-4 py-2 rounded-xl font-medium text-sm transition-all duration-200 transform
                                    @if ($isDisabled) bg-gray-100 text-gray-500 cursor-not-allowed
                                    @else
                                        bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 hover:scale-105 shadow-lg hover:shadow-xl @endif">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $buttonIcon }}"></path>
                                </svg>
                                {{ $buttonText }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($ads->hasPages())
                <div class="mt-8">
                    {{ $ads->links() }}
                </div>
            @endif
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
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Ads Available</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Check back later for new earning opportunities! We're constantly adding new campaigns.
                </p>
                <button onclick="location.reload()"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Refresh Page
                </button>
            </div>
        @endif
    </div>

    <!-- Guide Modal -->
    <div id="guideModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeGuideModal()"></div>

            <div
                class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-orange-500 to-purple-500 rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Screenshot Guide</h3>
                    </div>
                    <button onclick="closeGuideModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-6 text-sm text-gray-600">
                    <div class="bg-gradient-to-r from-orange-50 to-purple-50 rounded-xl p-4">
                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Valid Screenshot Requirements:
                        </h4>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <svg class="w-3 h-3 mr-2 mt-1 text-green-500 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                <span>Must show WhatsApp Status interface</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-3 h-3 mr-2 mt-1 text-green-500 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                <span>View count must be clearly visible</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-3 h-3 mr-2 mt-1 text-green-500 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                <span>Timestamp must be shown</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-3 h-3 mr-2 mt-1 text-green-500 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                <span>Screenshot must be taken after 24 hours</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-3 h-3 mr-2 mt-1 text-green-500 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                <span>File format: JPG or PNG only</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-3 h-3 mr-2 mt-1 text-green-500 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                <span>Maximum file size: 2MB</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl p-4">
                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-yellow-800 mb-1">Important Warning:</p>
                                <p class="text-yellow-700">Invalid screenshots will result in task rejection. After 3
                                    rejections, your account will be flagged and you won't be able to participate in
                                    future campaigns.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3 mt-8">
                    <button onclick="closeGuideModal()"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors duration-200">
                        Cancel
                    </button>
                    <button onclick="closeGuideModal()"
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-orange-500 to-purple-500 text-white text-sm font-medium rounded-xl hover:from-orange-600 hover:to-purple-600 transition-all duration-200 transform hover:scale-105">
                        Got it!
                    </button>
                </div>
            </div>
        </div>
    </div>
    <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

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
</style>

<script>
    function openGuideModal() {
        document.getElementById('guideModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Add entrance animation
        const modal = document.querySelector('#guideModal > div > div');
        modal.style.transform = 'scale(0.95)';
        modal.style.opacity = '0';

        setTimeout(() => {
            modal.style.transform = 'scale(1)';
            modal.style.opacity = '1';
            modal.style.transition = 'all 0.2s ease-out';
        }, 10);
    }

    function closeGuideModal() {
        const modal = document.querySelector('#guideModal > div > div');
        modal.style.transform = 'scale(0.95)';
        modal.style.opacity = '0';
        modal.style.transition = 'all 0.15s ease-in';

        setTimeout(() => {
            document.getElementById('guideModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 150);
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeGuideModal();
        }
    });

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
        // Animate stats cards
        const statsCards = document.querySelectorAll('.grid.grid-cols-2 > div');
        statsCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition =
                `opacity 0.6s ease-out ${index * 0.1}s, transform 0.6s ease-out ${index * 0.1}s`;
            observer.observe(card);
        });

        // Animate ad cards
        const adCards = document.querySelectorAll('.grid.gap-4 > div');
        adCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition =
                `opacity 0.6s ease-out ${index * 0.05}s, transform 0.6s ease-out ${index * 0.05}s`;
            observer.observe(card);
        });
    });

    // Add hover effects for ad cards
    document.addEventListener('DOMContentLoaded', function() {
        const adCards = document.querySelectorAll('.grid.gap-4 > div');
        adCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                const image = this.querySelector('img');
                if (image) {
                    image.style.transform = 'scale(1.05)';
                }
            });

            card.addEventListener('mouseleave', function() {
                const image = this.querySelector('img');
                if (image) {
                    image.style.transform = 'scale(1)';
                }
            });
        });
    });

    // Add click ripple effect for buttons
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('button:not([disabled])');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
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
        
        @keyframes progressPulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
        
        .progress-bar {
            animation: progressPulse 2s ease-in-out infinite;
        }
    `;
    document.head.appendChild(style);

    // Add progress bar animations
    document.addEventListener('DOMContentLoaded', function() {
        const progressBars = document.querySelectorAll('.bg-gradient-to-r.from-orange-500.to-purple-500');
        progressBars.forEach(bar => {
            if (bar.parentElement.classList.contains('bg-gray-200')) {
                bar.classList.add('progress-bar');
            }
        });
    });
</script>
</div>
