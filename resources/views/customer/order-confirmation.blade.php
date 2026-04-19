@extends('layouts.app')

@section('title', __('Order Details') . ' - ' . $order->order_number)

@section('content')
<div class="pb-4">
    <!-- Header -->
    <div class="sticky top-0 bg-white z-30 px-4 py-3 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <a href="{{ locale_route('home') }}" class="p-2 -ml-2">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold text-gray-800">{{ __('Order Details') }}</h1>
        </div>
    </div>

    <div class="px-4 mt-4 space-y-4">
        <!-- Success Banner -->
        @if($order->status === 'pending')
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">{{ __('Order Placed Successfully!') }}</h2>
            <p class="text-gray-600">{{ __("Thank you for your order. We'll start preparing it shortly.") }}</p>
        </div>
        @endif

        <!-- Order Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">{{ __('Order Status') }}</h3>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium
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
            
            <!-- Status Timeline -->
            <div class="relative">
                @php
                    $statuses = ['pending', 'accepted', 'preparing', 'ready', 'on_the_way', 'delivered'];
                    $currentIndex = array_search($order->status, $statuses);
                @endphp
                
                <div class="flex justify-between">
                    @foreach(['Pending', 'Accepted', 'Preparing', 'Ready', 'On Way', 'Delivered'] as $index => $label)
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium
                                @if($index <= $currentIndex) bg-primary-500 text-white @else bg-gray-200 text-gray-500 @endif">
                                @if($index < $currentIndex)
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                    </svg>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>
                            <span class="text-[10px] text-gray-500 mt-1 text-center">{{ __($label) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Order Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Order Information') }}</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Order Number') }}</span>
                    <span class="font-medium text-gray-800">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Date') }}</span>
                    <span class="text-gray-800">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Payment') }}</span>
                    <span class="text-gray-800 capitalize">{{ $order->payment_method }}</span>
                </div>
            </div>
        </div>

        <!-- Delivery Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Delivery Details') }}</h3>
            
            <div class="space-y-3">
                <div>
                    <span class="text-gray-500 text-sm">{{ __('Name') }}</span>
                    <p class="text-gray-800">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">{{ __('Phone') }}</span>
                    <p class="text-gray-800">{{ $order->customer_phone }}</p>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">{{ __('Address') }}</span>
                    <p class="text-gray-800">{{ $order->customer_address }}</p>
                </div>
                @if($order->notes)
                <div>
                    <span class="text-gray-500 text-sm">{{ __('Notes') }}</span>
                    <p class="text-gray-800">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Order Items') }}</h3>
            
            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex justify-between pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                        <div>
                            <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $item->quantity }}x {{ format_currency($item->unit_price) }}
                                @if($item->size_name)
                                    <span class="text-gray-400">- {{ $item->size_name }}</span>
                                @endif
                            </p>
                            @if($item->bundle_snapshot && count($item->bundle_snapshot))
                                <p class="text-xs text-gray-500 mt-1">
                                    @foreach($item->bundle_snapshot as $line)
                                        {{ $line['name'] }}@if(($line['quantity'] ?? 1) > 1) ×{{ $line['quantity'] }}@endif@if(!$loop->last), @endif
                                    @endforeach
                                </p>
                            @endif
                            @if($item->toppings->count() > 0)
                                <p class="text-xs text-gray-400">+ {{ $item->toppings->pluck('topping_name')->join(', ') }}</p>
                            @endif
                        </div>
                        <span class="font-medium text-gray-800">{{ format_currency($item->total_price) }}</span>
                    </div>
                @endforeach
            </div>
            
            <!-- Totals -->
            <div class="mt-4 pt-4 border-t border-gray-200 space-y-2">
                <div class="flex justify-between text-gray-600">
                    <span>{{ __('Subtotal') }}</span>
                    <span>{{ format_currency($order->subtotal) }}</span>
                </div>
                @if($order->delivery_fee > 0)
                <div class="flex justify-between text-gray-600">
                    <span>{{ __('Delivery Fee') }}</span>
                    <span>{{ format_currency($order->delivery_fee) }}</span>
                </div>
                @endif
                @if($order->discount > 0)
                <div class="flex justify-between text-green-600">
                    <span>{{ __('Discount') }}</span>
                    <span>-{{ currency_symbol() }} {{ number_format($order->discount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-semibold pt-2 border-t border-gray-200">
                    <span class="text-gray-800">{{ __('Total') }}</span>
                    <span class="text-primary-500">{{ format_currency($order->total) }}</span>
                </div>
            </div>
        </div>

        <!-- WhatsApp Support -->
        <a href="{{ $order->whatsapp_link }}" target="_blank"
           class="block w-full bg-green-500 hover:bg-green-600 text-white text-center font-semibold py-4 rounded-xl transition-colors">
            <span class="flex items-center justify-center gap-2">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                {{ __('Contact Us on WhatsApp') }}
            </span>
        </a>

        <!-- Back to Home -->
        <a href="{{ locale_route('home') }}" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-center font-semibold py-4 rounded-xl transition-colors">
            {{ __('Home') }}
        </a>
    </div>
</div>
@endsection
