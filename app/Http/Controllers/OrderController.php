<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Store a new order
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $orderItems = [];

            foreach ($request->input('items', []) as $rawItem) {
                if (! empty($rawItem['offer_id'])) {
                    $item = Validator::make($rawItem, [
                        'offer_id' => 'required|exists:offers,id',
                        'quantity' => 'required|integer|min:1',
                    ])->validate();

                    $offer = Offer::active()
                        ->with(['products' => function ($q) {
                            $q->orderByPivot('sort_order');
                        }])
                        ->findOrFail($item['offer_id']);

                    if ($offer->products->isEmpty()) {
                        throw new \InvalidArgumentException('Offer has no products.');
                    }

                    $firstProduct = $offer->products->first();
                    $bundleSnapshot = $offer->products->map(function ($p) {
                        return [
                            'product_id' => $p->id,
                            'name' => $p->display_name,
                            'quantity' => (int) $p->pivot->quantity,
                        ];
                    })->values()->all();

                    $qty = (int) $item['quantity'];
                    $unitPrice = (float) $offer->bundle_price;
                    $totalPrice = $unitPrice * $qty;
                    $subtotal += $totalPrice;

                    $orderItems[] = [
                        'offer_id' => $offer->id,
                        'product_id' => $firstProduct->id,
                        'product_name' => $offer->display_name.' (Offer)',
                        'size_id' => null,
                        'size_name' => null,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'toppings' => [],
                        'bundle_snapshot' => $bundleSnapshot,
                    ];
                } else {
                    $item = Validator::make($rawItem, [
                        'product_id' => 'required|exists:products,id',
                        'quantity' => 'required|integer|min:1',
                        'size_id' => 'nullable|exists:product_sizes,id',
                        'toppings' => 'nullable|array',
                        'toppings.*.id' => 'exists:product_toppings,id',
                    ])->validate();

                    $product = Product::with(['sizes', 'toppings'])->findOrFail($item['product_id']);

                    $unitPrice = (float) $product->base_price;
                    $sizeName = null;

                    if (! empty($item['size_id'])) {
                        $size = $product->sizes->find($item['size_id']);
                        if ($size) {
                            $unitPrice += (float) $size->price_modifier;
                            $sizeName = $size->display_name;
                        }
                    }

                    $toppingItems = [];
                    if (! empty($item['toppings'])) {
                        foreach ($item['toppings'] as $toppingData) {
                            $topping = $product->toppings->find($toppingData['id']);
                            if ($topping) {
                                $unitPrice += (float) $topping->price;
                                $toppingItems[] = [
                                    'topping_id' => $topping->id,
                                    'topping_name' => $topping->display_name,
                                    'price' => $topping->price,
                                ];
                            }
                        }
                    }

                    $totalPrice = $unitPrice * (int) $item['quantity'];
                    $subtotal += $totalPrice;

                    $orderItems[] = [
                        'offer_id' => null,
                        'product_id' => $product->id,
                        'product_name' => $product->display_name,
                        'size_id' => $item['size_id'] ?? null,
                        'size_name' => $sizeName,
                        'quantity' => (int) $item['quantity'],
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'toppings' => $toppingItems,
                        'bundle_snapshot' => null,
                    ];
                }
            }

            $order = Order::create([
                'customer_name' => $request->input('customer_name'),
                'customer_phone' => $request->input('customer_phone'),
                'customer_address' => $request->input('customer_address'),
                'notes' => $request->input('notes'),
                'subtotal' => $subtotal,
                'delivery_fee' => 0,
                'discount' => 0,
                'total' => $subtotal,
                'status' => 'pending',
                'payment_method' => 'cash',
            ]);

            foreach ($orderItems as $itemData) {
                $toppings = $itemData['toppings'];
                unset($itemData['toppings']);

                $orderItem = $order->items()->create($itemData);

                foreach ($toppings as $toppingData) {
                    $orderItem->toppings()->create($toppingData);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'tracking_token' => $order->tracking_token,
                    'total' => $order->total,
                    'tracking_url' => $order->tracking_url,
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Show order tracking form, or redirect to order if order_number provided
     */
    public function trackForm()
    {
        $orderNumber = request('order_number');
        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)->first();
            if ($order) {
                return redirect()->route('orders.track.show', [
                    'locale' => app()->getLocale(),
                    'token' => $order->tracking_token,
                ]);
            }
        }

        return view('customer.track');
    }

    /**
     * Track order by token
     */
    public function track(string $locale, string $token)
    {
        $order = Order::with(['items.toppings'])
            ->where('tracking_token', $token)
            ->firstOrFail();

        return view('customer.order-confirmation', compact('order'));
    }
}
