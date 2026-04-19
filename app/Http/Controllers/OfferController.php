<?php

namespace App\Http\Controllers;

use App\Models\Offer;

class OfferController extends Controller
{
    /**
     * Customer-facing offers (bundles) listing.
     */
    public function index()
    {
        $offers = Offer::active()
            ->ordered()
            ->with(['products' => function ($q) {
                $q->orderByPivot('sort_order');
            }])
            ->get();

        return view('customer.offers', compact('offers'));
    }
}
