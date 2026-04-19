<!-- Admin Header -->
<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6">
    <!-- Mobile Menu Button -->
    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    
    <!-- Page Title -->
    <h1 class="text-lg font-semibold text-gray-800">@yield('title', __('Dashboard'))</h1>
    
    <!-- Right Side -->
    <div class="flex items-center gap-4">
        @include('admin.components.locale-switcher')
        <!-- Notifications -->
        <button class="relative p-2 rounded-lg hover:bg-gray-100">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            @php $newOrders = \App\Models\Order::where('status', 'pending')->where('created_at', '>=', now()->subHours(1))->count(); @endphp
            @if($newOrders > 0)
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            @endif
        </button>
        
        <!-- Admin Profile -->
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                <span class="text-primary-600 font-semibold text-sm">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
            </div>
            <span class="hidden md:block text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</span>
        </div>
    </div>
</header>
