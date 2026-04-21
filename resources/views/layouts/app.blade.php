<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(site_favicon_url())
        <link rel="icon" href="{{ site_favicon_url() }}" type="image/x-icon">
    @else
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @endif

    @php
        $defaultMeta = site_setting('meta_description');
        $fallbackMeta = $defaultMeta ?: (site_name().' - '.__('Order Food Online'));
    @endphp
    <title>{{ site_name() }} - @yield('title', __('Order Food Online'))</title>
    <meta name="description" content="@yield('meta_description', $fallbackMeta)">
    @if(site_setting('meta_keywords'))
        <meta name="keywords" content="{{ site_setting('meta_keywords') }}">
    @endif

    <!-- Open Graph / SEO -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ site_name() }} - @yield('title', __('Order Food Online'))">
    <meta property="og:description" content="@yield('meta_description', $fallbackMeta)">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @elseif(site_og_image_url())
        <meta property="og:image" content="{{ site_og_image_url() }}">
    @endif
    <meta property="og:locale" content="{{ app()->getLocale() === 'de' ? 'de_DE' : 'en_US' }}">
    @if(app()->getLocale() === 'de')
    <meta property="og:locale:alternate" content="en_US">
    @else
    <meta property="og:locale:alternate" content="de_DE">
    @endif

    <!-- Canonical & Hreflang -->
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="en" href="{{ route('home', ['locale' => 'en']) }}">
    <link rel="alternate" hreflang="de" href="{{ route('home', ['locale' => 'de']) }}">
    <link rel="alternate" hreflang="x-default" href="{{ route('home', ['locale' => config('app.locale')]) }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.site-theme')
    <script>
        window.APP_CURRENCY = @json(currency_symbol());
        window.formatMoney = function (n) {
            return window.APP_CURRENCY + ' ' + Number(n).toFixed(2);
        };
        window.formatMoneyModifier = function (n) {
            var v = Number(n);
            return (v >= 0 ? '+ ' : '- ') + window.APP_CURRENCY + ' ' + Math.abs(v).toFixed(2);
        };
        window.formatMoneyPlus = function (n) {
            return '+ ' + window.APP_CURRENCY + ' ' + Number(n).toFixed(2);
        };
    </script>
    
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen font-sans antialiased" x-data>
    @php
        $currentPath = request()->path();
        $pathWithoutLocale = preg_replace('#^(en|de)/?#', '', $currentPath) ?: '';
        $urlEn = $pathWithoutLocale ? url('en/' . $pathWithoutLocale) : route('home', ['locale' => 'en']);
        $urlDe = $pathWithoutLocale ? url('de/' . $pathWithoutLocale) : route('home', ['locale' => 'de']);
    @endphp

    <!-- Mobile Top Bar -->
    <header class="lg:hidden sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-gray-100">
        <div class="px-4 h-14 flex items-center justify-between">
            <a href="{{ locale_route('home') }}" class="flex items-center gap-2 min-w-0">
                @if(site_logo_url())
                    <img src="{{ site_logo_url() }}" alt="{{ site_name() }}" class="h-8 w-auto max-w-[140px] object-contain">
                @else
                    <span class="text-lg font-bold text-primary-500 truncate">{{ site_name() }}</span>
                @endif
            </a>
            <div class="flex rounded-lg border border-gray-200 overflow-hidden shrink-0">
                <a href="{{ $urlEn }}" class="px-2.5 py-1 text-xs font-semibold {{ app()->getLocale() === 'en' ? 'bg-primary-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">EN</a>
                <a href="{{ $urlDe }}" class="px-2.5 py-1 text-xs font-semibold {{ app()->getLocale() === 'de' ? 'bg-primary-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">DE</a>
            </div>
        </div>
    </header>

    <!-- Desktop Navigation -->
    <nav class="hidden lg:block bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ locale_route('home') }}" class="flex items-center gap-2">
                        @if(site_logo_url())
                            <img src="{{ site_logo_url() }}" alt="{{ site_name() }}" class="h-9 w-auto max-w-[180px] object-contain">
                        @else
                            <span class="text-2xl font-bold text-primary-500">{{ site_name() }}</span>
                        @endif
                    </a>
                    <div class="ml-10 flex items-center space-x-4">
                        <a href="{{ locale_route('home') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('home') ? 'text-primary-600 bg-primary-50' : 'text-gray-600 hover:text-primary-500' }}">
                            {{ __('Home') }}
                        </a>
                        <a href="{{ locale_route('menu') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('menu') ? 'text-primary-600 bg-primary-50' : 'text-gray-600 hover:text-primary-500' }}">
                            {{ __('Menu') }}
                        </a>
                        <a href="{{ locale_route('offers') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('offers') ? 'text-primary-600 bg-primary-50' : 'text-gray-600 hover:text-primary-500' }}">
                            {{ __('Offers') }}
                        </a>
                        <a href="{{ locale_route('orders.track') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('orders.track*') ? 'text-primary-600 bg-primary-50' : 'text-gray-600 hover:text-primary-500' }}">
                            {{ __('Track Order') }}
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Language Switcher -->
                    <div class="flex rounded-lg border border-gray-200 overflow-hidden">
                        <a href="{{ $urlEn }}" class="px-3 py-1.5 text-sm font-medium {{ app()->getLocale() === 'en' ? 'bg-primary-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">EN</a>
                        <a href="{{ $urlDe }}" class="px-3 py-1.5 text-sm font-medium {{ app()->getLocale() === 'de' ? 'bg-primary-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">DE</a>
                    </div>
                    <form action="{{ locale_route('menu') }}" method="GET" class="hidden md:block">
                        <div class="relative">
                            <input type="text" name="search" placeholder="{{ __('Search foods...') }}" 
                                   class="w-64 bg-gray-100 rounded-xl py-2 pl-10 pr-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </form>
                    <a href="{{ locale_route('cart') }}" class="relative p-2 text-gray-600 hover:text-primary-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span x-show="$store.cart.count > 0" 
                              x-text="$store.cart.count"
                              class="absolute -top-1 -right-1 bg-primary-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-medium"></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-6">
        <div class="bg-white min-h-screen lg:min-h-0 lg:rounded-2xl lg:shadow-sm pb-20 lg:pb-6">
            @yield('content')
        </div>
    </div>
    
    @include('components.site-social')
    @include('components.bottom-nav')
    @include('components.cart-button')
    @include('components.chat-widget')
    
    @stack('scripts')

    @php($gaId = trim((string) site_setting('google_analytics_id', '')) ?: config('services.google.analytics_id'))
    @if($gaId)
    <!-- Google Analytics (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', @json($gaId), {
            page_path: window.location.pathname,
            send_page_view: true
        });
    </script>
    @endif
</body>
</html>
