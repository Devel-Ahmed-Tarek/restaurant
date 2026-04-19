<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Product;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analytics
    ) {}

    public function index(Request $request)
    {
        [$from, $to] = $this->analytics->resolveRange(
            $request->input('date_from'),
            $request->input('date_to')
        );

        $offerId = $request->filled('offer_id') ? (int) $request->input('offer_id') : null;
        $productId = $request->filled('product_id') ? (int) $request->input('product_id') : null;

        if ($offerId) {
            $productId = null;
        }

        $topProducts = $this->analytics->topProducts($from, $to, 15);
        $topOffers = $this->analytics->topOffers($from, $to, 15);
        $ordersByHour = $this->analytics->ordersCountByHour($from, $to);
        $ordersByWeekday = $this->analytics->ordersCountByWeekday($from, $to);
        $summary = $this->analytics->summary($from, $to);

        $weekdayLabels = AnalyticsService::weekdayLabels();

        $selectedProduct = null;
        $selectedOffer = null;
        $chartProduct = null;
        $chartOffer = null;

        if ($productId) {
            $selectedProduct = Product::find($productId);
            if ($selectedProduct) {
                $chartProduct = [
                    'name' => $selectedProduct->name,
                    'byHour' => array_values($this->analytics->productQuantityByHour($from, $to, $productId)),
                    'byWeekday' => array_values($this->analytics->productQuantityByWeekday($from, $to, $productId)),
                ];
            }
        } elseif ($offerId) {
            $selectedOffer = Offer::find($offerId);
            if ($selectedOffer) {
                $chartOffer = [
                    'name' => $selectedOffer->name,
                    'byHour' => array_values($this->analytics->offerQuantityByHour($from, $to, $offerId)),
                    'byWeekday' => array_values($this->analytics->offerQuantityByWeekday($from, $to, $offerId)),
                ];
            }
        }

        $chartData = [
            'hourLabels' => range(0, 23),
            'ordersByHour' => array_values($ordersByHour),
            'weekdayLabels' => $weekdayLabels,
            'ordersByWeekday' => array_values($ordersByWeekday),
            'topProducts' => [
                'labels' => $topProducts->pluck('product_name')->all(),
                'values' => $topProducts->pluck('qty')->map(fn ($v) => (int) $v)->all(),
            ],
            'topOffers' => [
                'labels' => $topOffers->pluck('name')->all(),
                'values' => $topOffers->pluck('qty')->map(fn ($v) => (int) $v)->all(),
            ],
            'product' => $chartProduct,
            'offer' => $chartOffer,
            'strings' => [
                'qty' => __('Qty'),
                'orders' => __('Orders'),
                'bundlesSold' => __('Bundles sold'),
                'titleTopProducts' => __('Top products (quantity)'),
                'titleTopOffers' => __('Top offers (quantity)'),
                'titleOrdersByHour' => __('Orders by hour (all)'),
                'titleOrdersByWeekday' => __('Orders by weekday (all)'),
            ],
        ];

        if ($chartProduct) {
            $chartData['strings']['productHour'] = __('Product: :name — quantity by hour', ['name' => $chartProduct['name']]);
            $chartData['strings']['productWeekday'] = __('Product: :name — quantity by weekday', ['name' => $chartProduct['name']]);
        }
        if ($chartOffer) {
            $chartData['strings']['offerHour'] = __('Offer: :name — bundles by hour', ['name' => $chartOffer['name']]);
            $chartData['strings']['offerWeekday'] = __('Offer: :name — bundles by weekday', ['name' => $chartOffer['name']]);
        }

        $products = Product::orderBy('name')->get(['id', 'name']);
        $offers = Offer::orderBy('name')->get(['id', 'name']);

        return view('admin.analytics.index', [
            'from' => $from,
            'to' => $to,
            'summary' => $summary,
            'topProducts' => $topProducts,
            'topOffers' => $topOffers,
            'chartData' => $chartData,
            'products' => $products,
            'offers' => $offers,
            'selectedProductId' => $productId && $selectedProduct ? $productId : null,
            'selectedOfferId' => $offerId && $selectedOffer ? $offerId : null,
        ]);
    }
}
