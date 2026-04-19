<!-- Mobile Sidebar Overlay -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden"
     @click="sidebarOpen = false"
     style="display: none;">
</div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed lg:static inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-50 transform transition-transform duration-300 lg:translate-x-0">
    <!-- Logo -->
    <div class="h-16 flex items-center justify-center border-b border-gray-200">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
            <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.2-1.1-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
                </svg>
            </div>
            <span class="text-xl font-bold text-gray-800">{{ __('Foodlay') }}</span>
        </a>
    </div>
    
    <!-- Navigation -->
    <nav class="p-4 space-y-1">
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
            </svg>
            <span class="font-medium">{{ __('Dashboard') }}</span>
        </a>

        <a href="{{ route('admin.analytics.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.analytics.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
            </svg>
            <span class="font-medium">{{ __('Analytics') }}</span>
        </a>
        
        <a href="{{ route('admin.orders.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
            </svg>
            <span class="font-medium">{{ __('Orders') }}</span>
            @php $pendingOrders = \App\Models\Order::where('status', 'pending')->count(); @endphp
            @if($pendingOrders > 0)
                <span class="ml-auto bg-primary-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingOrders }}</span>
            @endif
        </a>
        
        <a href="{{ route('admin.categories.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l-5.5 9h11z"/>
                <circle cx="17.5" cy="17.5" r="4.5"/>
                <path d="M3 13.5h8v8H3z"/>
            </svg>
            <span class="font-medium">{{ __('Categories') }}</span>
        </a>
        
        <a href="{{ route('admin.products.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.products.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.06 22.99h1.66c.84 0 1.53-.64 1.63-1.46L23 5.05l-5 1V1h-1.97v5.05l-5-1 1.66 16.48c.09.82.78 1.46 1.62 1.46h1.66L18.06 22.99zM1 21.99V21h15.03v.99c0 .55-.45 1-1 1H2c-.55 0-1-.45-1-1zm15.03-7H1V8c0-.55.45-1 1-1h13.03c.55 0 1 .45 1 1v6.99z"/>
            </svg>
            <span class="font-medium">{{ __('Products') }}</span>
        </a>

        <a href="{{ route('admin.offers.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.offers.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/>
            </svg>
            <span class="font-medium">{{ __('Offers') }}</span>
        </a>

        <a href="{{ route('admin.settings.edit') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.14 12.94c.04-.31.06-.63.06-.94 0-.31-.02-.63-.06-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.04.31-.06.63-.06.94s.02.63.06.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
            </svg>
            <span class="font-medium">{{ __('Site settings') }}</span>
        </a>
        
        <hr class="my-4 border-gray-200">
        
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                </svg>
                <span class="font-medium">{{ __('Logout') }}</span>
            </button>
        </form>
    </nav>
</aside>
