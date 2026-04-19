@extends('layouts.app')

@section('title', __('Home'))

@section('content')
<div class="pb-4">
    <!-- Header (Mobile Only) -->
    <div class="lg:hidden bg-primary-500 text-white px-4 pt-4 pb-8 rounded-b-3xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    @if(site_logo_url())
                        <img src="{{ site_logo_url() }}" alt="{{ site_name() }}" class="h-8 w-auto max-w-[140px] object-contain brightness-0 invert">
                    @else
                        {{ site_name() }}
                    @endif
                </h1>
                <div class="flex items-center gap-1 text-white/80 text-sm mt-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span>{{ __('Delivery to your location') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ locale_route('cart') }}" class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center relative">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                    <span x-show="$store.cart.count > 0" 
                          x-text="$store.cart.count"
                          class="absolute -top-1 -right-1 bg-white text-primary-500 text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold"></span>
                </a>
            </div>
        </div>
        
        <!-- Search Bar -->
        <form action="{{ locale_route('menu') }}" method="GET">
            <div class="relative">
                <input type="text" name="search" placeholder="{{ __('Search foods...') }}" 
                       class="w-full bg-white rounded-xl py-3 pl-12 pr-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-300">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>
    </div>

    <!-- Banner Slider -->
    @if($specialProducts->count() > 0)
    <div class="px-4 lg:px-6 -mt-4 lg:mt-6">
        <div class="bg-gradient-to-r from-red-500 to-pink-500 rounded-2xl p-4 lg:p-8 text-white relative overflow-hidden">
            <div class="relative z-10 lg:max-w-lg">
                <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full">{{ __('SPECIAL OFFER') }}</span>
                <h3 class="text-xl lg:text-3xl font-bold mt-2">{{ __("Today's Special Menu") }}</h3>
                <p class="text-white/80 text-sm lg:text-base mt-1">{{ __('Up to 50% OFF') }}</p>
                <a href="{{ locale_route('menu') }}" class="inline-block bg-white text-primary-500 font-semibold px-4 lg:px-6 py-2 lg:py-3 rounded-lg mt-3 text-sm lg:text-base hover:bg-gray-100 transition-colors">
                    {{ __('Order Now') }}
                </a>
            </div>
            <div class="absolute right-0 bottom-0 w-32 h-32 lg:w-64 lg:h-64 bg-white/10 rounded-full -mr-8 -mb-8 lg:-mr-16 lg:-mb-16"></div>
            <div class="hidden lg:block absolute right-10 top-1/2 -translate-y-1/2 w-48 h-48 bg-white/10 rounded-full"></div>
        </div>
    </div>
    @endif

    <!-- Categories -->
    <div class="mt-6 px-4 lg:px-6">
        <h2 class="text-lg lg:text-xl font-semibold text-gray-800 mb-4">{{ __("What's on your Mind?") }}</h2>
        <div class="flex lg:flex-wrap gap-4 overflow-x-auto lg:overflow-x-visible hide-scrollbar pb-2">
            @foreach($categories as $category)
                <a href="{{ locale_route('menu', ['category' => $category->id]) }}" 
                   class="flex flex-col items-center min-w-[70px] lg:min-w-[90px] group">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 bg-primary-50 rounded-full flex items-center justify-center text-3xl lg:text-4xl mb-2 group-hover:bg-primary-100 group-hover:scale-105 transition-all duration-200">
                        @if($category->image)
                            <img src="{{ Storage::url($category->image) }}" alt="{{ $category->display_name }}" class="w-12 h-12 lg:w-16 lg:h-16 rounded-full object-cover">
                        @else
                            {{ $category->icon ?? '🍽️' }}
                        @endif
                    </div>
                    <span class="text-xs lg:text-sm text-gray-600 text-center group-hover:text-primary-500 transition-colors">{{ $category->display_name }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Special Deals -->
    @if($specialProducts->count() > 0)
    <div class="mt-6 lg:mt-8 px-4 lg:px-6">
        <div class="text-center mb-4 lg:mb-6">
            <p class="text-primary-500 text-sm font-medium">{{ __("Don't Miss Today's Deal!") }}</p>
            <h2 class="text-xl lg:text-2xl font-bold text-gray-800">{{ __("Let's Take a Bite Today") }}</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
            @foreach($specialProducts as $product)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow relative group">
                    <div class="flex lg:flex-col">
                        <div class="w-32 h-32 lg:w-full lg:h-48 bg-gray-100 flex-shrink-0">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->display_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-4xl lg:text-6xl">🍽️</div>
                            @endif
                        </div>
                        <div class="flex-1 p-4 flex flex-col justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-800 lg:text-lg">{{ $product->display_name }}</h3>
                                <p class="text-gray-500 text-sm line-clamp-2 mt-1">{{ $product->display_description }}</p>
                            </div>
                            <div class="flex items-center justify-between mt-3">
                                <div>
                                    @if($product->old_price)
                                        <span class="text-gray-400 text-sm line-through">{{ format_currency($product->old_price) }}</span>
                                    @endif
                                    <span class="text-primary-500 font-bold text-lg block lg:inline ml-0 lg:ml-1">{{ format_currency($product->base_price) }}</span>
                                </div>
                                <button @click="$dispatch('open-product', {{ $product->id }})" 
                                        class="bg-primary-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-600 transition-colors">
                                    {{ __('Add to Cart') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Featured Products -->
    @if($featuredProducts->count() > 0)
    <div class="mt-6 lg:mt-10 px-4 lg:px-6">
        <div class="flex items-center justify-between mb-4 lg:mb-6">
            <h2 class="text-lg lg:text-xl font-semibold text-gray-800">{{ __('Popular Items') }}</h2>
            <a href="{{ locale_route('menu') }}" class="text-primary-500 text-sm lg:text-base font-medium hover:text-primary-600 transition-colors">{{ __('See All') }} →</a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 lg:gap-6">
            @foreach($featuredProducts as $product)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
                    <div class="aspect-square bg-gray-100 relative overflow-hidden">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->display_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl">🍽️</div>
                        @endif
                        
                        @if(!$product->is_available)
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                <span class="bg-gray-800 text-white text-xs px-3 py-1 rounded-full">{{ __('Unavailable') }}</span>
                            </div>
                        @endif
                        
                        <button @click="$dispatch('open-product', {{ $product->id }})"
                                class="absolute bottom-2 right-2 w-8 h-8 lg:w-10 lg:h-10 bg-primary-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-primary-600 hover:scale-110 transition-all duration-200">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-3 lg:p-4">
                        <h3 class="font-medium text-gray-800 text-sm lg:text-base line-clamp-1">{{ $product->display_name }}</h3>
                        <div class="flex items-center gap-1 mt-1 lg:mt-2">
                            @if($product->old_price)
                                <span class="text-gray-400 text-xs line-through">{{ number_format($product->old_price, 2) }}</span>
                            @endif
                            <span class="text-primary-500 font-bold lg:text-lg">{{ format_currency($product->base_price) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Product Modal Component -->
@include('components.product-modal')
@endsection
