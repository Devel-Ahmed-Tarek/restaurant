<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public function resolveRange(?string $dateFrom, ?string $dateTo): array
    {
        $to = $dateTo ? Carbon::parse($dateTo)->endOfDay() : now()->endOfDay();
        $from = $dateFrom
            ? Carbon::parse($dateFrom)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    public function baseOrderQuery(Carbon $from, Carbon $to)
    {
        return Order::query()
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$from, $to]);
    }

    /**
     * @return Collection<int, object{product_id: int, product_name: string, qty: int}>
     */
    public function topProducts(Carbon $from, Carbon $to, int $limit = 15): Collection
    {
        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNull('order_items.offer_id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('order_items.product_id')
            ->selectRaw('order_items.product_id')
            ->selectRaw('MAX(order_items.product_name) as product_name')
            ->selectRaw('SUM(order_items.quantity) as qty')
            ->orderByDesc('qty')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, object{offer_id: int, name: string, qty: int}>
     */
    public function topOffers(Carbon $from, Carbon $to, int $limit = 15): Collection
    {
        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('offers', 'offers.id', '=', 'order_items.offer_id')
            ->whereNotNull('order_items.offer_id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('order_items.offer_id')
            ->selectRaw('order_items.offer_id')
            ->selectRaw('COALESCE(MAX(offers.name), MAX(order_items.product_name)) as name')
            ->selectRaw('SUM(order_items.quantity) as qty')
            ->orderByDesc('qty')
            ->limit($limit)
            ->get();
    }

    /**
     * Orders count per hour (0–23) for all orders in range.
     *
     * @return array<int, int> 24 integers
     */
    public function ordersCountByHour(Carbon $from, Carbon $to): array
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $rows = $this->baseOrderQuery($from, $to)
                ->selectRaw('HOUR(created_at) as h')
                ->selectRaw('COUNT(*) as c')
                ->groupBy('h')
                ->orderBy('h')
                ->pluck('c', 'h');
        } else {
            // SQLite
            $rows = $this->baseOrderQuery($from, $to)
                ->selectRaw("cast(strftime('%H', created_at) as integer) as h")
                ->selectRaw('COUNT(*) as c')
                ->groupBy('h')
                ->orderBy('h')
                ->pluck('c', 'h');
        }

        return $this->fillHours($rows);
    }

    /**
     * Sum of item quantities per hour for a single product (regular lines only, not bundles).
     *
     * @return array<int, int>
     */
    public function productQuantityByHour(Carbon $from, Carbon $to, int $productId): array
    {
        $driver = DB::getDriverName();

        $q = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNull('order_items.offer_id')
            ->where('order_items.product_id', $productId)
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$from, $to]);

        if ($driver === 'mysql') {
            $rows = (clone $q)
                ->selectRaw('HOUR(orders.created_at) as h')
                ->selectRaw('SUM(order_items.quantity) as q')
                ->groupBy('h')
                ->orderBy('h')
                ->pluck('q', 'h');
        } else {
            $rows = (clone $q)
                ->selectRaw("cast(strftime('%H', orders.created_at) as integer) as h")
                ->selectRaw('SUM(order_items.quantity) as q')
                ->groupBy('h')
                ->orderBy('h')
                ->pluck('q', 'h');
        }

        return $this->fillHours($rows);
    }

    /**
     * Orders count per weekday: Mon–Sun (index 0 = Monday).
     *
     * @return array<int, int> 7 integers
     */
    public function ordersCountByWeekday(Carbon $from, Carbon $to): array
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // WEEKDAY: Mon=0 ... Sun=6
            $rows = $this->baseOrderQuery($from, $to)
                ->selectRaw('WEEKDAY(created_at) as wd')
                ->selectRaw('COUNT(*) as c')
                ->groupBy('wd')
                ->orderBy('wd')
                ->pluck('c', 'wd');
        } else {
            $rows = $this->baseOrderQuery($from, $to)
                ->selectRaw("(cast(strftime('%w', created_at) as integer) + 6) % 7 as wd")
                ->selectRaw('COUNT(*) as c')
                ->groupBy('wd')
                ->orderBy('wd')
                ->pluck('c', 'wd');
        }

        return $this->fillWeekdays($rows);
    }

    /**
     * Product quantities per weekday (Mon–Sun).
     *
     * @return array<int, int>
     */
    public function productQuantityByWeekday(Carbon $from, Carbon $to, int $productId): array
    {
        $driver = DB::getDriverName();

        $q = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNull('order_items.offer_id')
            ->where('order_items.product_id', $productId)
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$from, $to]);

        if ($driver === 'mysql') {
            $rows = (clone $q)
                ->selectRaw('WEEKDAY(orders.created_at) as wd')
                ->selectRaw('SUM(order_items.quantity) as q')
                ->groupBy('wd')
                ->orderBy('wd')
                ->pluck('q', 'wd');
        } else {
            $rows = (clone $q)
                ->selectRaw("(cast(strftime('%w', orders.created_at) as integer) + 6) % 7 as wd")
                ->selectRaw('SUM(order_items.quantity) as q')
                ->groupBy('wd')
                ->orderBy('wd')
                ->pluck('q', 'wd');
        }

        return $this->fillWeekdays($rows);
    }

    /**
     * Bundle (offer) sales quantity per hour.
     *
     * @return array<int, int>
     */
    public function offerQuantityByHour(Carbon $from, Carbon $to, int $offerId): array
    {
        $driver = DB::getDriverName();

        $q = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.offer_id', $offerId)
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$from, $to]);

        if ($driver === 'mysql') {
            $rows = (clone $q)
                ->selectRaw('HOUR(orders.created_at) as h')
                ->selectRaw('SUM(order_items.quantity) as q')
                ->groupBy('h')
                ->orderBy('h')
                ->pluck('q', 'h');
        } else {
            $rows = (clone $q)
                ->selectRaw("cast(strftime('%H', orders.created_at) as integer) as h")
                ->selectRaw('SUM(order_items.quantity) as q')
                ->groupBy('h')
                ->orderBy('h')
                ->pluck('q', 'h');
        }

        return $this->fillHours($rows);
    }

    /**
     * Bundle sales quantity per weekday (Mon–Sun).
     *
     * @return array<int, int>
     */
    public function offerQuantityByWeekday(Carbon $from, Carbon $to, int $offerId): array
    {
        $driver = DB::getDriverName();

        $q = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.offer_id', $offerId)
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$from, $to]);

        if ($driver === 'mysql') {
            $rows = (clone $q)
                ->selectRaw('WEEKDAY(orders.created_at) as wd')
                ->selectRaw('SUM(order_items.quantity) as q')
                ->groupBy('wd')
                ->orderBy('wd')
                ->pluck('q', 'wd');
        } else {
            $rows = (clone $q)
                ->selectRaw("(cast(strftime('%w', orders.created_at) as integer) + 6) % 7 as wd")
                ->selectRaw('SUM(order_items.quantity) as q')
                ->groupBy('wd')
                ->orderBy('wd')
                ->pluck('q', 'wd');
        }

        return $this->fillWeekdays($rows);
    }

    public function summary(Carbon $from, Carbon $to): array
    {
        $q = $this->baseOrderQuery($from, $to);

        return [
            'orders_count' => (clone $q)->count(),
            'revenue' => (float) (clone $q)->sum('total'),
        ];
    }

    /**
     * @param  Collection|array  $rows  keyed by hour
     * @return array<int, int>
     */
    private function fillHours($rows): array
    {
        $rows = collect($rows);
        $out = [];
        for ($h = 0; $h < 24; $h++) {
            $out[$h] = (int) ($rows[$h] ?? 0);
        }

        return $out;
    }

    /**
     * @param  Collection|array  $rows  keyed by weekday 0–6 Mon-first
     * @return array<int, int>
     */
    private function fillWeekdays($rows): array
    {
        $rows = collect($rows);
        $out = [];
        for ($wd = 0; $wd < 7; $wd++) {
            $out[$wd] = (int) ($rows[$wd] ?? 0);
        }

        return $out;
    }

    public static function weekdayLabels(): array
    {
        return array_map(
            static fn (string $d) => __($d),
            ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
        );
    }
}
