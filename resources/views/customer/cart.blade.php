@extends('layouts.app')

@section('title', __('Cart'))

@section('content')
<div class="pb-4" x-data="cartPage()">
    <!-- Header (Mobile) -->
    <div class="lg:hidden sticky top-0 bg-white z-30 px-4 py-3 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <a href="{{ locale_route('menu') }}" class="p-2 -ml-2">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold text-gray-800">{{ __('My Cart') }}</h1>
        </div>
    </div>
    
    <!-- Desktop Header -->
    <div class="hidden lg:block px-6 py-6 border-b border-gray-100">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('Shopping Cart') }}</h1>
        <p class="text-gray-500 mt-1">{{ __('Review your items before checkout') }}</p>
    </div>

    <!-- Empty Cart -->
    <div x-show="$store.cart.items.length === 0" class="px-4 lg:px-6 py-16 text-center">
        <div class="text-6xl lg:text-7xl mb-4">🛒</div>
        <h2 class="text-xl lg:text-2xl font-semibold text-gray-800 mb-2">{{ __('Your cart is empty') }}</h2>
        <p class="text-gray-500 mb-6">{{ __('Add some delicious items to your cart') }}</p>
        <a href="{{ locale_route('menu') }}" class="inline-block bg-primary-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary-600 transition-colors">
            {{ __('Browse Menu') }}
        </a>
    </div>

    <!-- Cart Items (Desktop: Two Column Layout) -->
    <div x-show="$store.cart.items.length > 0" class="px-4 lg:px-6 mt-4 lg:mt-6">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <template x-for="item in $store.cart.items" :key="item.key">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                        <div class="flex gap-4">
                            <!-- Image -->
                            <div class="w-20 h-20 lg:w-24 lg:h-24 bg-gray-100 rounded-xl flex-shrink-0 overflow-hidden">
                                <template x-if="item.image">
                                    <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-full h-full flex items-center justify-center text-3xl lg:text-4xl">🍽️</div>
                                </template>
                            </div>
                            
                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 lg:text-lg truncate" x-text="item.name"></h3>
                                
                                <!-- Size / Toppings / Bundle contents -->
                                <div class="text-sm text-gray-500 mt-1">
                                    <template x-if="item.type === 'offer' && item.bundle_lines?.length">
                                        <span x-text="item.bundle_lines.map(l => l.name + (l.qty > 1 ? ' ×' + l.qty : '')).join(' · ')"></span>
                                    </template>
                                    <template x-if="item.type !== 'offer' && item.size">
                                        <span x-text="'{{ __('Size') }}: ' + item.size.name"></span>
                                    </template>
                                    <template x-if="item.type !== 'offer' && item.toppings && item.toppings.length > 0">
                                        <span x-text="', + ' + item.toppings.map(t => t.name).join(', ')"></span>
                                    </template>
                                </div>
                                
                                <div class="flex items-center justify-between mt-3">
                                    <!-- Price -->
                                    <span class="text-primary-500 font-bold lg:text-lg" x-text="window.formatMoney(item.price * item.quantity)"></span>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center gap-2 lg:gap-3">
                                        <button @click="$store.cart.update(item.key, item.quantity - 1)" 
                                                class="w-8 h-8 lg:w-10 lg:h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center font-semibold text-gray-800 lg:text-lg" x-text="item.quantity"></span>
                                        <button @click="$store.cart.update(item.key, item.quantity + 1)" 
                                                class="w-8 h-8 lg:w-10 lg:h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Remove Button -->
                            <button @click="$store.cart.remove(item.key)" class="text-gray-400 hover:text-red-500 transition-colors self-start p-1">
                                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
                
                <!-- Continue Shopping Link (Desktop) -->
                <div class="hidden lg:block pt-4">
                    <a href="{{ locale_route('menu') }}" class="inline-flex items-center gap-2 text-primary-500 hover:text-primary-600 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        {{ __('Continue Shopping') }}
                    </a>
                </div>
            </div>
            
            <!-- Order Summary (Desktop: Sticky Sidebar) -->
            <div class="lg:col-span-1">
                <div x-show="$store.cart.items.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-6 mt-6 lg:mt-0 lg:sticky lg:top-24">
                    <h3 class="font-semibold text-gray-800 lg:text-lg mb-4">{{ __('Order Summary') }}</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-600">
                            <span>{{ __('Subtotal') }} (<span x-text="$store.cart.count"></span> {{ __('items') }})</span>
                            <span x-text="window.formatMoney($store.cart.total)"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>{{ __('Delivery Fee') }}</span>
                            <span class="text-green-600">{{ __('Free') }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between text-lg font-semibold">
                            <span class="text-gray-800">{{ __('Total') }}</span>
                            <span class="text-primary-500" x-text="window.formatMoney($store.cart.total)"></span>
                        </div>
                    </div>
                    
                    <!-- Checkout Button -->
                    <div class="mt-6">
                        <a href="{{ locale_route('checkout') }}" class="block w-full bg-primary-500 hover:bg-primary-600 text-white text-center font-semibold py-4 rounded-xl transition-colors">
                            {{ __('Proceed to Checkout') }}
                        </a>
                    </div>
                    
                    <!-- Payment Icons -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500 text-center">{{ __('Cash on Delivery Available') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cartPage() {
    return {
        // Cart is managed by the global Alpine store
    }
}
</script>
@endpush
@endsection
