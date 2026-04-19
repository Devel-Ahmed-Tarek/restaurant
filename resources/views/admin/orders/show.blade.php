@extends('layouts.admin')

@section('title', __('Order') . ' ' . $order->order_number)

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('Back to Orders') }}
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ __('Order') }} {{ $order->order_number }}</h2>
            <p class="text-gray-500 text-sm">{{ $order->created_at->format('F d, Y \a\t H:i') }}</p>
        </div>
        
        <span class="inline-flex items-center gap-1 px-4 py-2 rounded-full text-sm font-medium
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
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('Order Items') }}</h3>
                
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                            @if($item->product?->image)
                                <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product_name }}" class="w-16 h-16 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-2xl">🍽️</div>
                            @endif
                            
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                                        @if($item->size_name)
                                            <p class="text-sm text-gray-500">{{ __('Size') }}: {{ $item->size_name }}</p>
                                        @endif
                                        @if($item->bundle_snapshot && count($item->bundle_snapshot))
                                            <p class="text-sm text-gray-600">
                                                {{ __('Bundle:') }} @foreach($item->bundle_snapshot as $line){{ $line['name'] }}@if(($line['quantity'] ?? 1) > 1) ×{{ $line['quantity'] }}@endif@if(!$loop->last), @endif @endforeach
                                            </p>
                                        @endif
                                        @if($item->toppings->count() > 0)
                                            <p class="text-sm text-gray-500">
                                                {{ __('Toppings:') }} {{ $item->toppings->pluck('topping_name')->join(', ') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-800">{{ format_currency($item->total_price) }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->quantity }} x {{ format_currency($item->unit_price) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Totals -->
                <div class="mt-6 pt-4 border-t border-gray-200 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">{{ __('Subtotal') }}</span>
                        <span class="text-gray-800">{{ format_currency($order->subtotal) }}</span>
                    </div>
                    @if($order->delivery_fee > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">{{ __('Delivery Fee') }}</span>
                            <span class="text-gray-800">{{ format_currency($order->delivery_fee) }}</span>
                        </div>
                    @endif
                    @if($order->discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">{{ __('Discount') }}</span>
                            <span class="text-green-600">-{{ currency_symbol() }} {{ number_format($order->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-semibold pt-2 border-t border-gray-200">
                        <span class="text-gray-800">{{ __('Total') }}</span>
                        <span class="text-primary-500">{{ format_currency($order->total) }}</span>
                    </div>
                </div>
            </div>

            @if($order->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-2">{{ __('Notes') }}</h3>
                    <p class="text-gray-600">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('Customer Details') }}</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Name') }}</p>
                        <p class="font-medium text-gray-800">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Phone') }}</p>
                        <a href="tel:{{ $order->customer_phone }}" class="font-medium text-primary-500">{{ $order->customer_phone }}</a>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Address') }}</p>
                        <p class="text-gray-800">{{ $order->customer_address }}</p>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('Update Status') }}</h3>
                
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <select name="status" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @foreach(\App\Models\Order::STATUS_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>{{ __($label) }}</option>
                        @endforeach
                    </select>
                    
                    <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition-colors">
                        {{ __('Update Status') }}
                    </button>
                </form>
            </div>

            <!-- Payment Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('Payment') }}</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Method') }}</span>
                        <span class="font-medium text-gray-800 capitalize">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Status') }}</span>
                        <span class="font-medium {{ $order->is_paid ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $order->is_paid ? __('Paid') : __('Pending') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
