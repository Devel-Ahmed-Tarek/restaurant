@extends('layouts.admin')

@section('title', __('Analytics'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ __('Sales analytics') }}</h2>
            <p class="text-gray-500 text-sm mt-1">{{ __('Most ordered items, peak hours, and weekday patterns (excludes cancelled orders).') }}</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.analytics.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-wrap gap-4 items-end">
        <div>
            <label for="date_from" class="block text-xs font-medium text-gray-500 mb-1">{{ __('From') }}</label>
            <input type="date" name="date_from" id="date_from" value="{{ $from->format('Y-m-d') }}"
                   class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label for="date_to" class="block text-xs font-medium text-gray-500 mb-1">{{ __('To') }}</label>
            <input type="date" name="date_to" id="date_to" value="{{ $to->format('Y-m-d') }}"
                   class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary-500">
        </div>
        <div class="min-w-[200px] flex-1">
            <label for="product_id" class="block text-xs font-medium text-gray-500 mb-1">{{ __('Product detail (optional)') }}</label>
            <select name="product_id" id="product_id" onchange="if(this.value) document.getElementById('offer_id').value=''"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">{{ __('— None —') }}</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ (int) $selectedProductId === (int) $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[200px] flex-1">
            <label for="offer_id" class="block text-xs font-medium text-gray-500 mb-1">{{ __('Offer detail (optional)') }}</label>
            <select name="offer_id" id="offer_id" onchange="if(this.value) document.getElementById('product_id').value=''"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">{{ __('— None —') }}</option>
                @foreach($offers as $o)
                    <option value="{{ $o->id }}" {{ (int) ($selectedOfferId ?? 0) === (int) $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Apply') }}</button>
    </form>

    <!-- Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">{{ __('Orders in period') }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($summary['orders_count']) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">{{ __('Revenue (:currency)', ['currency' => currency_symbol()]) }}</p>
            <p class="text-3xl font-bold text-primary-600 mt-1">{{ format_currency($summary['revenue']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Top products -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h3 class="font-semibold text-gray-800 mb-3">{{ __('Top dishes (products)') }}</h3>
            @if($topProducts->isEmpty())
                <p class="text-sm text-gray-500 py-8 text-center">{{ __('No product line items in this range.') }}</p>
            @else
                <div class="h-72 relative">
                    <canvas id="chart-top-products"></canvas>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">{{ __('Products') }}</th><th class="pb-2 text-right">{{ __('Qty') }}</th></tr></thead>
                        <tbody>
                            @foreach($topProducts as $row)
                                <tr class="border-b border-gray-50">
                                    <td class="py-2 text-gray-800">{{ $row->product_name }}</td>
                                    <td class="py-2 text-right font-medium">{{ number_format($row->qty) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Top offers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h3 class="font-semibold text-gray-800 mb-3">{{ __('Top offers (bundles)') }}</h3>
            @if($topOffers->isEmpty())
                <p class="text-sm text-gray-500 py-8 text-center">{{ __('No offer sales in this range.') }}</p>
            @else
                <div class="h-72 relative">
                    <canvas id="chart-top-offers"></canvas>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">{{ __('Offers') }}</th><th class="pb-2 text-right">{{ __('Qty') }}</th></tr></thead>
                        <tbody>
                            @foreach($topOffers as $row)
                                <tr class="border-b border-gray-50">
                                    <td class="py-2 text-gray-800">{{ $row->name }}</td>
                                    <td class="py-2 text-right font-medium">{{ number_format($row->qty) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- All orders: hour + weekday -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h3 class="font-semibold text-gray-800 mb-3">{{ __('Peak hours — all orders') }}</h3>
            <p class="text-xs text-gray-500 mb-2">{{ __('How many orders were placed in each hour of the day.') }}</p>
            <div class="h-72 relative">
                <canvas id="chart-orders-hour"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h3 class="font-semibold text-gray-800 mb-3">{{ __('Orders by weekday — all') }}</h3>
            <p class="text-xs text-gray-500 mb-2">{{ __('Monday → Sunday.') }}</p>
            <div class="h-72 relative">
                <canvas id="chart-orders-weekday"></canvas>
            </div>
        </div>
    </div>

    @if($selectedProductId)
        <div class="bg-primary-50 border border-primary-100 rounded-xl p-4">
            <h3 class="font-semibold text-gray-900">{{ __('Product focus:') }} {{ $products->firstWhere('id', $selectedProductId)?->name ?? '—' }}</h3>
            <p class="text-sm text-gray-600 mt-1">{{ __('Quantities for this product only (single-item lines, not part of a bundle).') }}</p>
        </div>
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="h-72 relative">
                    <canvas id="chart-product-hour"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="h-72 relative">
                    <canvas id="chart-product-weekday"></canvas>
                </div>
            </div>
        </div>
    @elseif($selectedOfferId)
        <div class="bg-primary-50 border border-primary-100 rounded-xl p-4">
            <h3 class="font-semibold text-gray-900">{{ __('Offer focus:') }} {{ $offers->firstWhere('id', $selectedOfferId)?->name ?? '—' }}</h3>
            <p class="text-sm text-gray-600 mt-1">{{ __('Number of bundles sold per hour / weekday.') }}</p>
        </div>
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="h-72 relative">
                    <canvas id="chart-offer-hour"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="h-72 relative">
                    <canvas id="chart-offer-weekday"></canvas>
                </div>
            </div>
        </div>
    @else
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 text-center text-gray-600 text-sm">
            {{ __('Select a product or an offer in the filters above to see hourly and weekday breakdown for that item only.') }}
        </div>
    @endif

    <script type="application/json" id="analytics-chart-data">@json($chartData)</script>
</div>

@push('scripts')
    @vite(['resources/js/admin-analytics.js'])
@endpush
@endsection
