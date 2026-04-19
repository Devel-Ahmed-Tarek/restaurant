<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Show cart page
     */
    public function index()
    {
        return view('customer.cart');
    }

    /**
     * Show checkout page
     */
    public function checkout()
    {
        return view('customer.checkout');
    }
}
