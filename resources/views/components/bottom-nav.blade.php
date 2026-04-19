<!-- Bottom Navigation for Mobile Only -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40">
    <div class="flex justify-around items-center h-16 max-w-lg mx-auto">
        <a href="{{ locale_route('home') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('home') ? 'text-primary-500' : 'text-gray-500' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
            <span class="text-xs mt-1">{{ __('Home') }}</span>
        </a>
        
        <a href="{{ locale_route('menu') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('menu') ? 'text-primary-500' : 'text-gray-500' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.2-1.1-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
            </svg>
            <span class="text-xs mt-1">{{ __('Menu') }}</span>
        </a>

        <a href="{{ locale_route('offers') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('offers') ? 'text-primary-500' : 'text-gray-500' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/>
            </svg>
            <span class="text-xs mt-1">{{ __('Offers') }}</span>
        </a>
        
        <a href="{{ locale_route('cart') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('cart') ? 'text-primary-500' : 'text-gray-500' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
            </svg>
            <span class="text-xs mt-1">{{ __('Cart') }}</span>
            <!-- Cart Count Badge -->
            <span x-show="$store.cart.count > 0" 
                  x-text="$store.cart.count"
                  class="absolute -top-1 right-4 bg-primary-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
            </span>
        </a>
        
        <a href="{{ locale_route('orders.track') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('orders.*') ? 'text-primary-500' : 'text-gray-500' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
            </svg>
            <span class="text-xs mt-1">{{ __('Orders') }}</span>
        </a>
    </div>
</nav>
