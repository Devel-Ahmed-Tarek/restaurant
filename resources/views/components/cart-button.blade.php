<!-- Floating Cart Button (Mobile Only - shows when items in cart and not on cart page) -->
<div x-show="$store.cart.count > 0 && !window.location.pathname.includes('/cart')" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-4"
     class="lg:hidden fixed bottom-20 left-4 right-4 max-w-lg mx-auto z-30"
     style="display: none;">
    <a href="{{ locale_route('cart') }}" 
       class="flex items-center justify-between bg-primary-500 text-white rounded-xl px-4 py-3 shadow-lg hover:bg-primary-600 transition-colors">
        <div class="flex items-center gap-3">
            <div class="bg-white/20 rounded-lg p-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
            </div>
            <div>
                <span class="font-semibold">{{ __('View Cart') }}</span>
                <span class="text-white/80 text-sm ml-2">(<span x-text="$store.cart.count"></span> {{ __('items') }})</span>
            </div>
        </div>
        <div class="font-bold text-lg">
            <span x-text="window.formatMoney($store.cart.total)"></span>
        </div>
    </a>
</div>
