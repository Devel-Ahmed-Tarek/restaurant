@extends('layouts.app')

@section('title', __('Menu'))

@section('content')
<div class="pb-4">
    <!-- Header (Mobile version with back button) -->
    <div class="lg:hidden sticky top-0 bg-white z-30 px-4 py-3 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <a href="{{ locale_route('home') }}" class="p-2 -ml-2">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            
            <!-- Search -->
            <form action="{{ locale_route('menu') }}" method="GET" class="flex-1">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search foods...') }}" 
                           class="w-full bg-gray-100 rounded-xl py-2.5 pl-10 pr-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Desktop Header -->
    <div class="hidden lg:block px-6 py-6 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ __('Our Menu') }}</h1>
                <p class="text-gray-500 mt-1">{{ __('Explore our delicious food items') }}</p>
            </div>
            <form action="{{ locale_route('menu') }}" method="GET" class="w-80">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search foods...') }}" 
                           class="w-full bg-gray-100 rounded-xl py-3 pl-12 pr-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>
        </div>
    </div>
        
    <!-- Categories Filter -->
    <div class="sticky top-0 lg:top-16 bg-white z-20 py-3 lg:py-4 border-b border-gray-100">
        <div class="flex gap-2 lg:gap-3 overflow-x-auto hide-scrollbar px-4 lg:px-6">
            <a href="{{ locale_route('menu', request()->only('search')) }}" 
               class="flex-shrink-0 px-4 lg:px-5 py-2 lg:py-2.5 rounded-full text-sm font-medium transition-colors {{ !request('category') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ __('All') }}
            </a>
            @foreach($categories as $category)
                <a href="{{ locale_route('menu', array_merge(request()->only('search'), ['category' => $category->id])) }}" 
                   class="flex-shrink-0 px-4 lg:px-5 py-2 lg:py-2.5 rounded-full text-sm font-medium transition-colors {{ request('category') == $category->id ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $category->icon }} {{ $category->display_name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Products Grid -->
    <div class="px-4 lg:px-6 mt-4 lg:mt-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800 lg:text-lg">{{ __('Food List') }}</h2>
            <span class="text-sm text-gray-500">{{ $products->total() }} {{ __('items') }}</span>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 lg:gap-6">
            @forelse($products as $product)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
                    <div class="aspect-square bg-gray-100 relative overflow-hidden">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->display_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl">🍽️</div>
                        @endif
                        
                        <!-- Tags -->
                        @if($product->tags && count($product->tags) > 0)
                            <div class="absolute top-2 left-2 flex flex-wrap gap-1">
                                @if(in_array('new', $product->tags))
                                    <span class="bg-blue-500 text-white text-[10px] lg:text-xs px-2 py-0.5 rounded-full">{{ __('NEW') }}</span>
                                @endif
                                @if(in_array('popular', $product->tags))
                                    <span class="bg-yellow-500 text-white text-[10px] lg:text-xs px-2 py-0.5 rounded-full">{{ __('HOT') }}</span>
                                @endif
                            </div>
                        @endif
                        
                        @if(!$product->is_available)
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                <span class="bg-gray-800 text-white text-xs px-3 py-1 rounded-full">{{ __('Unavailable') }}</span>
                            </div>
                        @else
                            <button @click="$dispatch('open-product', {{ $product->id }})"
                                    class="absolute bottom-2 right-2 w-8 h-8 lg:w-10 lg:h-10 bg-primary-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-primary-600 hover:scale-110 transition-all duration-200">
                                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                    <div class="p-3 lg:p-4">
                        <p class="text-xs lg:text-sm text-gray-500">{{ $product->category->display_name }}</p>
                        <h3 class="font-medium text-gray-800 text-sm lg:text-base line-clamp-2 mt-0.5">{{ $product->display_name }}</h3>
                        <div class="flex items-center gap-1 mt-2">
                            @if($product->old_price)
                                <span class="text-gray-400 text-xs line-through">{{ number_format($product->old_price, 2) }}</span>
                            @endif
                            <span class="text-primary-500 font-bold lg:text-lg">{{ format_currency($product->base_price) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    <div class="text-5xl lg:text-6xl mb-4">🍽️</div>
                    <p class="lg:text-lg">{{ __('No products found') }}</p>
                    @if(request('search') || request('category'))
                        <a href="{{ locale_route('menu') }}" class="text-primary-500 mt-2 inline-block hover:text-primary-600">{{ __('View all products') }}</a>
                    @endif
                </div>
            @endforelse
        </div>
        
        @if($products->hasPages())
            <div class="mt-6 lg:mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Product Modal Component -->
@include('components.product-modal')
@endsection
