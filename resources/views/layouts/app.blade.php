@php
    $settingService = app(\App\Services\SettingService::class);
    $seoSettings = $settingService->getSeoSettings();
    $brandingSettings = $settingService->getBrandingSettings();
    $siteName = $brandingSettings['site_name'] ?? config('app.name', 'Laravel');
    $pageTitle = isset($title) ? $title . ' - ' . $siteName : $siteName;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Title -->
        <title>{{ $pageTitle }}</title>
        
        <!-- SEO Meta Tags -->
        @if($seoSettings['seo_description'])
            <meta name="description" content="{{ $seoSettings['seo_description'] }}">
        @endif
        
        @if($seoSettings['seo_keywords'])
            <meta name="keywords" content="{{ $seoSettings['seo_keywords'] }}">
        @endif
        
        <!-- Favicon -->
        @if($brandingSettings['site_favicon'])
            <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $brandingSettings['site_favicon']) }}">
        @else
            <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        @endif
        
        <!-- OpenGraph Meta Tags -->
        <meta property="og:title" content="{{ $seoSettings['og_title'] ?? $pageTitle }}">
        <meta property="og:description" content="{{ $seoSettings['og_description'] ?? $seoSettings['seo_description'] ?? 'Welcome to ' . $siteName }}">
        <meta property="og:type" content="{{ $seoSettings['og_type'] ?? 'website' }}">
        <meta property="og:url" content="{{ request()->url() }}">
        <meta property="og:site_name" content="{{ $siteName }}">
        
        @if($seoSettings['og_image'])
            <meta property="og:image" content="{{ asset('storage/' . $seoSettings['og_image']) }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
        @endif
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="{{ $seoSettings['twitter_card'] ?? 'summary_large_image' }}">
        <meta name="twitter:title" content="{{ $seoSettings['og_title'] ?? $pageTitle }}">
        <meta name="twitter:description" content="{{ $seoSettings['og_description'] ?? $seoSettings['seo_description'] ?? 'Welcome to ' . $siteName }}">
        
        @if($seoSettings['twitter_site'])
            <meta name="twitter:site" content="@{{ ltrim($seoSettings['twitter_site'], '@') }}">
        @endif
        
        @if($seoSettings['twitter_creator'])
            <meta name="twitter:creator" content="@{{ ltrim($seoSettings['twitter_creator'], '@') }}">
        @endif
        
        @if($seoSettings['og_image'])
            <meta name="twitter:image" content="{{ asset('storage/' . $seoSettings['og_image']) }}">
        @endif
        
        <!-- Additional Meta Tags -->
        <meta name="robots" content="index, follow">
        <meta name="author" content="{{ $siteName }}">
        <meta name="theme-color" content="#f97316">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ request()->url() }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Additional Head Content -->
        @stack('head')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Header -->
            @livewire('partials.header')

            <!-- Ban Message -->
            @auth
                @if(auth()->user()->isBannedFromBatches())
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mx-4 sm:mx-6 lg:mx-8 mt-4 rounded-lg shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-red-800">
                                    Account Restricted
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>
                                        You have been banned from joining batches.
                                        @if(auth()->user()->ban_reason)
                                            <br><strong>Reason:</strong> {{ auth()->user()->ban_reason }}
                                        @endif
                                        @if(auth()->user()->banned_from_batches_at)
                                            <br><strong>Date:</strong> {{ auth()->user()->banned_from_batches_at->format('M d, Y \\a\\t g:i A') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" onclick="this.parentElement.parentElement.parentElement.parentElement.style.display='none'" class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50">
                                        <span class="sr-only">Dismiss</span>
                                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            @livewire('partials.footer')
        </div>
    </body>
</html>
