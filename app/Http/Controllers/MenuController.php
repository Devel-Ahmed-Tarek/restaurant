<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Show menu page
     */
    public function index(Request $request)
    {
        $categories = Category::active()->ordered()->get();
        
        $query = Product::with(['category', 'sizes', 'toppings'])
            ->available();
        
        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_de', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $products = $query->latest()->paginate(12);
        
        return view('customer.menu', compact('categories', 'products'));
    }

    /**
     * Get product details (API)
     */
    public function show(string $locale, Product $product)
    {
        $product->load(['category', 'sizes', 'toppings']);
        
        $sizes = $product->sizes->map(function ($size) {
            $row = $size->toArray();
            $row['name'] = $size->display_name;

            return $row;
        })->values();

        $toppings = $product->toppings->map(function ($topping) {
            $row = $topping->toArray();
            $row['name'] = $topping->display_name;

            return $row;
        })->values();

        return response()->json([
            'id' => $product->id,
            'name' => $product->display_name,
            'description' => $product->display_description,
            'image' => $product->image_url,
            'base_price' => $product->base_price,
            'old_price' => $product->old_price,
            'is_available' => $product->is_available,
            'tags' => $product->tags ?? [],
            'category' => ['id' => $product->category->id, 'name' => $product->category->display_name],
            'sizes' => $sizes,
            'toppings' => $toppings,
        ]);
    }
}
