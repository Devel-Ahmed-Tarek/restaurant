@extends('layouts.app')

@section('title', __('Track Order'))

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
            <h1 class="text-lg font-semibold text-gray-800">{{ __('Track Order') }}</h1>
        </div>
    </div>

    <div class="px-4 mt-8">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-primary-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">{{ __('Track Your Order') }}</h2>
            <p class="text-gray-500">{{ __('Enter your order number') }}</p>
        </div>

        <form action="{{ locale_route('orders.track') }}" method="GET" class="space-y-4" x-data="{ orderNumber: '' }">
            <div>
                <label for="order_number" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Order Number') }}</label>
                <input type="text" id="order_number" name="order_number" x-model="orderNumber" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="{{ __('Enter your order number') }}">
            </div>
            
            <button type="submit" 
                    class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-4 rounded-xl transition-colors">
                {{ __('Track') }}
            </button>
        </form>

        @php($waDigits = preg_replace('/\D/', '', (string) site_setting('whatsapp_number', '')))
        <div class="mt-8 p-4 bg-gray-50 rounded-2xl">
            <h3 class="font-semibold text-gray-800 mb-2">{{ __('Need Help?') }}</h3>
            <p class="text-gray-600 text-sm mb-4">{{ __('If you have any questions about your order, feel free to contact us.') }}</p>
            @if($waDigits !== '')
            <a href="https://wa.me/{{ $waDigits }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 text-green-600 font-medium">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                {{ __('Contact on WhatsApp') }}
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
