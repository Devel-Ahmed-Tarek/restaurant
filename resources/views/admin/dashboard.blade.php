@extends('layouts.admin')

@section('title', __('Dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Today's Orders -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __("Today's Orders") }}</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['today_orders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Today's Revenue -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __("Today's Revenue") }}</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ format_currency($stats['today_revenue']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Pending Orders -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Pending Orders') }}</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['pending_orders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Total Products -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ __('Total Products') }}</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_products'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.06 22.99h1.66c.84 0 1.53-.64 1.63-1.46L23 5.05l-5 1V1h-1.97v5.05l-5-1 1.66 16.48c.09.82.78 1.46 1.62 1.46h1.66L18.06 22.99zM1 21.99V21h15.03v.99c0 .55-.45 1-1 1H2c-.55 0-1-.45-1-1zm15.03-7H1V8c0-.55.45-1 1-1h13.03c.55 0 1 .45 1 1v6.99z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">{{ __('Pending Orders') }}</h2>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-sm text-primary-500 hover:text-primary-600">{{ __('View All') }}</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($pendingOrders as $order)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800">{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-500">{{ $order->customer_name }} - {{ $order->customer_phone }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">{{ format_currency($order->total) }}</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="mt-2 flex gap-2">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs bg-primary-50 text-primary-600 px-3 py-1 rounded-full">{{ __('View') }}</a>
                            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="accepted">
                                <button type="submit" class="text-xs bg-green-50 text-green-600 px-3 py-1 rounded-full">{{ __('Accept') }}</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <p>{{ __('No pending orders') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">{{ __('Recent Orders') }}</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-primary-500 hover:text-primary-600">{{ __('View All') }}</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="block p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800">{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-500">{{ $order->customer_name }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-1 text-xs rounded-full 
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($order->status === 'accepted') bg-blue-100 text-blue-700
                                    @elseif($order->status === 'preparing') bg-indigo-100 text-indigo-700
                                    @elseif($order->status === 'ready') bg-purple-100 text-purple-700
                                    @elseif($order->status === 'on_the_way') bg-orange-100 text-orange-700
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700
                                    @endif
                                ">
                                    {{ $order->status_label }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <p>{{ __('No orders yet') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
