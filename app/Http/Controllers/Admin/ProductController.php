<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\ProductTopping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_de', 'like', "%{$search}%");
            });
        }
        
        $products = $query->latest()->paginate(15);
        $categories = Category::ordered()->get();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        $initialSizes = [];
        $initialToppings = [];

        return view('admin.products.form', [
            'product' => null,
            'categories' => $categories,
            'initialSizes' => $initialSizes,
            'initialToppings' => $initialToppings,
        ]);
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'name_de' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_de' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'base_price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'tags' => 'nullable|array',
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'required|string|max:255',
            'sizes.*.name_de' => 'nullable|string|max:255',
            'sizes.*.price_modifier' => 'required|numeric',
            'toppings' => 'nullable|array',
            'toppings.*.name' => 'required|string|max:255',
            'toppings.*.name_de' => 'nullable|string|max:255',
            'toppings.*.price' => 'required|numeric|min:0',
            'toppings.*.is_required' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $productData = collect($validated)->except(['sizes', 'toppings', 'image'])->toArray();
            
            if ($request->hasFile('image')) {
                $productData['image'] = $request->file('image')->store('products', 'public');
            }
            
            $productData['is_available'] = $request->boolean('is_available', true);
            $productData['is_featured'] = $request->boolean('is_featured', false);
            
            $product = Product::create($productData);
            
            // Create sizes
            if (!empty($validated['sizes'])) {
                foreach ($validated['sizes'] as $index => $sizeData) {
                    $product->sizes()->create([
                        'name' => $sizeData['name'],
                        'name_de' => $sizeData['name_de'] ?? null,
                        'price_modifier' => $sizeData['price_modifier'],
                        'sort_order' => $index,
                    ]);
                }
            }
            
            // Create toppings
            if (!empty($validated['toppings'])) {
                foreach ($validated['toppings'] as $toppingData) {
                    $product->toppings()->create([
                        'name' => $toppingData['name'],
                        'name_de' => $toppingData['name_de'] ?? null,
                        'price' => $toppingData['price'],
                        'is_required' => $toppingData['is_required'] ?? false,
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('admin.products.index')
                ->with('success', __('Product created successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', __('Failed to create product: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Show the form for editing the product
     */
    public function edit(Product $product)
    {
        $product->load(['sizes', 'toppings']);
        $categories = Category::active()->ordered()->get();
        $initialSizes = $product->sizes->map(fn ($s) => [
            'name' => $s->name,
            'name_de' => $s->name_de,
            'price_modifier' => $s->price_modifier,
        ])->toArray();
        $initialToppings = $product->toppings->map(fn ($t) => [
            'name' => $t->name,
            'name_de' => $t->name_de,
            'price' => $t->price,
            'is_required' => (bool) $t->is_required,
        ])->toArray();

        return view('admin.products.form', compact('product', 'categories', 'initialSizes', 'initialToppings'));
    }

    /**
     * Update the product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'name_de' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_de' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'base_price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'tags' => 'nullable|array',
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'required|string|max:255',
            'sizes.*.name_de' => 'nullable|string|max:255',
            'sizes.*.price_modifier' => 'required|numeric',
            'toppings' => 'nullable|array',
            'toppings.*.name' => 'required|string|max:255',
            'toppings.*.name_de' => 'nullable|string|max:255',
            'toppings.*.price' => 'required|numeric|min:0',
            'toppings.*.is_required' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $productData = collect($validated)->except(['sizes', 'toppings', 'image'])->toArray();
            
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $productData['image'] = $request->file('image')->store('products', 'public');
            }
            
            $productData['is_available'] = $request->boolean('is_available', true);
            $productData['is_featured'] = $request->boolean('is_featured', false);
            
            $product->update($productData);
            
            // Sync sizes
            $product->sizes()->delete();
            if (!empty($validated['sizes'])) {
                foreach ($validated['sizes'] as $index => $sizeData) {
                    $product->sizes()->create([
                        'name' => $sizeData['name'],
                        'name_de' => $sizeData['name_de'] ?? null,
                        'price_modifier' => $sizeData['price_modifier'],
                        'sort_order' => $index,
                    ]);
                }
            }
            
            // Sync toppings
            $product->toppings()->delete();
            if (!empty($validated['toppings'])) {
                foreach ($validated['toppings'] as $toppingData) {
                    $product->toppings()->create([
                        'name' => $toppingData['name'],
                        'name_de' => $toppingData['name_de'] ?? null,
                        'price' => $toppingData['price'],
                        'is_required' => $toppingData['is_required'] ?? false,
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('admin.products.index')
                ->with('success', __('Product updated successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', __('Failed to update product: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Remove the product
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        
        return redirect()
            ->route('admin.products.index')
            ->with('success', __('Product deleted successfully!'));
    }

    /**
     * Toggle product availability
     */
    public function toggle(Product $product)
    {
        $product->update(['is_available' => !$product->is_available]);
        
        return back()->with('success', __('Product status updated!'));
    }
}
