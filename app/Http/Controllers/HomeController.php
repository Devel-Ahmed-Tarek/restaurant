<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the home page
     */
    public function index()
    {
        $categories = Category::active()->ordered()->get();
        $featuredProducts = Product::with('category')
            ->available()
            ->featured()
            ->latest()
            ->take(6)
            ->get();
        $specialProducts = Product::with('category')
            ->available()
            ->whereNotNull('old_price')
            ->where('old_price', '>', 0)
            ->latest()
            ->take(4)
            ->get();
            
        return view('customer.home', compact('categories', 'featuredProducts', 'specialProducts'));
    }
}
