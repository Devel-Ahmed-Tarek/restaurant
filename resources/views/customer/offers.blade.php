@extends('layouts.app')

@section('title', __('Offers'))

@section('content')
<div class="pb-4">
    <div class="lg:hidden sticky top-0 bg-white z-30 px-4 py-3 border-b border-gray-100">
        <h1 class="text-lg font-semibold text-gray-800">{{ __('Offers') }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ __('Bundles & combos at a special price') }}</p>
    </div>

    <div class="hidden lg:block px-6 py-6 border-b border-gray-100">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('Offers') }}</h1>
        <p class="text-gray-500 mt-1">{{ __('Bundles & combos at a special price') }}</p>
    </div>

    <div class="px-4 lg:px-6 mt-4 lg:mt-6">
        @forelse($offers as $offer)
            @php
                $payload = [
                    'offer_id' => $offer->id,
                    'name' => $offer->display_name,
                    'price' => (float) $offer->bundle_price,
                    'image' => $offer->image_url,
                    'bundle_lines' => $offer->products->map(function ($p) {
                        return [
                            'name' => $p->display_name,
                            'qty' => (int) $p->pivot->quantity,
                        ];
                    })->values()->all(),
                ];
                $ref = $offer->reference_total;
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4 lg:mb-6">
                <div class="flex flex-col sm:flex-row">
                    <div class="sm:w-48 lg:w-56 h-48 sm:h-auto bg-gray-100 flex-shrink-0">
                        @if($offer->image_url)
                            <img src="{{ $offer->image_url }}" alt="{{ $offer->display_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-6xl">🎁</div>
                        @endif
                    </div>
                    <div class="flex-1 p-4 lg:p-6 flex flex-col justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $offer->display_name }}</h2>
                            @if($offer->display_description)
                                <p class="text-gray-600 text-sm mt-2">{{ $offer->display_description }}</p>
                            @endif
                            <ul class="mt-3 space-y-1 text-sm text-gray-700">
                                @foreach($offer->products as $p)
                                    <li class="flex items-center gap-2">
                                        <span class="text-primary-500">✓</span>
                                        <span>{{ $p->display_name }} × {{ (int) $p->pivot->quantity }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            @if($ref > (float) $offer->bundle_price)
                                <p class="text-xs text-gray-500 mt-2">
                                    {{ __('If bought separately') }}: <span class="line-through">{{ format_currency($ref) }}</span>
                                </p>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-end justify-between gap-4 mt-4 pt-4 border-t border-gray-100">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Offer price') }}</p>
                                <p class="text-2xl font-bold text-primary-500">{{ format_currency($offer->bundle_price) }}</p>
                            </div>
                            <button type="button"
                                    @click="$store.cart.addOffer(@js($payload))"
                                    class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-6 py-3 rounded-xl transition-colors">
                                {{ __('Add to Cart') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-gray-500">
                <div class="text-6xl mb-4">🎁</div>
                <p>{{ __('No offers at the moment. Check back soon!') }}</p>
                <a href="{{ locale_route('menu') }}" class="inline-block mt-4 text-primary-500 font-medium hover:text-primary-600">{{ __('Browse Menu') }}</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
