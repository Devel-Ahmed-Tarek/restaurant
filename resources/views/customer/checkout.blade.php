@extends('layouts.app')

@section('title', __('Checkout'))

@section('content')
<div class="pb-4" x-data="checkoutPage()">
    <!-- Header (Mobile) -->
    <div class="lg:hidden sticky top-0 bg-white z-30 px-4 py-3 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <a href="{{ locale_route('cart') }}" class="p-2 -ml-2">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold text-gray-800">{{ __('Checkout') }}</h1>
        </div>
    </div>
    
    <!-- Desktop Header -->
    <div class="hidden lg:block px-6 py-6 border-b border-gray-100">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('Checkout') }}</h1>
        <p class="text-gray-500 mt-1">{{ __('Complete your order') }}</p>
    </div>

    <!-- Empty Cart Redirect -->
    <template x-if="$store.cart.items.length === 0">
        <div class="px-4 lg:px-6 py-16 text-center">
            <div class="text-6xl lg:text-7xl mb-4">🛒</div>
            <h2 class="text-xl lg:text-2xl font-semibold text-gray-800 mb-2">{{ __('Your cart is empty') }}</h2>
            <p class="text-gray-500 mb-6">{{ __('Add some items before checkout') }}</p>
            <a href="{{ locale_route('menu') }}" class="inline-block bg-primary-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary-600 transition-colors">
                {{ __('Browse Menu') }}
            </a>
        </div>
    </template>

    <!-- Checkout Form (Desktop: Two Column Layout) -->
    <form x-show="$store.cart.items.length > 0" @submit.prevent="placeOrder()" class="px-4 lg:px-6 mt-4 lg:mt-6">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <!-- Left Column: Forms -->
            <div class="lg:col-span-2 space-y-4 lg:space-y-6">
                <!-- Delivery Information -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-6">
                    <h3 class="font-semibold text-gray-800 lg:text-lg mb-4">{{ __('Delivery Information') }}</h3>
                    
                    <div class="space-y-4">
                        <div class="lg:grid lg:grid-cols-2 lg:gap-4 space-y-4 lg:space-y-0">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Full Name') }} *</label>
                                <input type="text" id="customer_name" x-model="form.customer_name" required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="{{ __('Enter your name') }}">
                            </div>
                            
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Phone Number') }} *</label>
                                <input type="tel" id="customer_phone" x-model="form.customer_phone" required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="01xxxxxxxxx">
                            </div>
                        </div>
                        
                        <div>
                            <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Delivery Address') }} *</label>
                            <textarea id="customer_address" x-model="form.customer_address" rows="3" required
                                      class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="{{ __('Enter your full address including building, floor, apartment...') }}"></textarea>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Order Notes (Optional)') }}</label>
                            <textarea id="notes" x-model="form.notes" rows="2"
                                      class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="{{ __('Any special instructions...') }}"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-6">
                    <h3 class="font-semibold text-gray-800 lg:text-lg mb-4">{{ __('Payment Method') }}</h3>
                    
                    <label class="flex items-center gap-3 p-4 rounded-xl border border-primary-500 bg-primary-50 cursor-pointer hover:bg-primary-100 transition-colors">
                        <input type="radio" name="payment" value="cash" checked class="w-5 h-5 text-primary-500">
                        <div class="flex-1">
                            <span class="font-medium text-gray-800 lg:text-lg">{{ __('Cash on Delivery') }}</span>
                            <p class="text-sm text-gray-500">{{ __('Pay when you receive your order') }}</p>
                        </div>
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </label>
                </div>
            </div>
            
            <!-- Right Column: Order Summary -->
            <div class="lg:col-span-1 mt-4 lg:mt-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-6 lg:sticky lg:top-24">
                    <h3 class="font-semibold text-gray-800 lg:text-lg mb-4">{{ __('Order Summary') }}</h3>
                    
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <template x-for="item in $store.cart.items" :key="item.key">
                            <div class="flex justify-between text-sm py-2 border-b border-gray-100">
                                <span class="text-gray-600">
                                    <span x-text="item.quantity" class="font-medium"></span>x <span x-text="item.name"></span>
                                    <template x-if="item.type === 'offer' && item.bundle_lines?.length">
                                        <span class="text-gray-400 block text-xs" x-text="item.bundle_lines.map(l => l.name + (l.qty > 1 ? ' ×' + l.qty : '')).join(', ')"></span>
                                    </template>
                                    <template x-if="item.type !== 'offer' && item.size">
                                        <span class="text-gray-400 block text-xs" x-text="item.size.name"></span>
                                    </template>
                                </span>
                                <span class="text-gray-800 font-medium" x-text="window.formatMoney(item.price * item.quantity)"></span>
                            </div>
                        </template>
                    </div>
                    
                    <div class="pt-4 space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>{{ __('Subtotal') }}</span>
                            <span x-text="formatMoney($store.cart.total)"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>{{ __('Delivery Fee') }}</span>
                            <span class="text-green-600">{{ __('Free') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold pt-3 border-t border-gray-200">
                            <span class="text-gray-800">{{ __('Total') }}</span>
                            <span class="text-primary-500" x-text="window.formatMoney($store.cart.total)"></span>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div x-show="error" x-text="error" class="mt-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm"></div>

                    <!-- Place Order Button -->
                    <button type="submit" 
                            :disabled="loading"
                            class="mt-6 w-full bg-primary-500 hover:bg-primary-600 disabled:bg-primary-300 text-white font-semibold py-4 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <span x-show="loading" class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full"></span>
                        <span x-text="loading ? '{{ __("Placing Order...") }}' : '{{ __("Place Order") }}'"></span>
                    </button>
                    
                    <p class="mt-3 text-xs text-gray-500 text-center">{{ __('By placing this order, you agree to our terms of service') }}</p>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function checkoutPage() {
    return {
        loading: false,
        error: null,
        form: {
            customer_name: '',
            customer_phone: '',
            customer_address: '',
            notes: ''
        },
        
        async placeOrder() {
            this.loading = true;
            this.error = null;
            
            try {
                // Prepare order items
                const items = Alpine.store('cart').items.map(item => {
                    if (item.type === 'offer') {
                        return {
                            offer_id: item.offer_id,
                            quantity: item.quantity,
                        };
                    }
                    return {
                        product_id: item.product_id,
                        quantity: item.quantity,
                        size_id: item.size?.id || null,
                        toppings: item.toppings?.map(t => ({ id: t.id })) || [],
                    };
                });
                
                const response = await fetch('{{ locale_route("orders.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ...this.form,
                        items: items
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Clear cart
                    Alpine.store('cart').clear();
                    
                    // Redirect to confirmation page
                    window.location.href = data.order.tracking_url;
                } else {
                    this.error = data.message || 'Failed to place order. Please try again.';
                }
            } catch (e) {
                console.error('Error placing order:', e);
                this.error = 'Failed to place order. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection
