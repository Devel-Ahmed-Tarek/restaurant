<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::withCount('products')
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.offers.index', compact('offers'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $initialLines = old('lines', [['product_id' => '', 'quantity' => 1]]);

        return view('admin.offers.form', [
            'offer' => null,
            'products' => $products,
            'initialLines' => $initialLines,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_de' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_de' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'bundle_price' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $data = collect($validated)->except(['lines', 'image'])->toArray();
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('offers', 'public');
            }
            $data['is_active'] = $request->boolean('is_active', true);
            $data['sort_order'] = $data['sort_order'] ?? 0;

            $offer = Offer::create($data);

            $offer->products()->sync($this->mergeOfferLines($validated['lines']));

            DB::commit();

            return redirect()
                ->route('admin.offers.index')
                ->with('success', __('Offer created successfully.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function edit(Offer $offer)
    {
        $offer->load('products');
        $products = Product::orderBy('name')->get();

        $initialLines = old('lines');
        if ($initialLines === null) {
            $initialLines = $offer->products->map(function ($p) {
                return [
                    'product_id' => (string) $p->id,
                    'quantity' => (int) $p->pivot->quantity,
                ];
            })->values()->all();
            if (count($initialLines) === 0) {
                $initialLines = [['product_id' => '', 'quantity' => 1]];
            }
        }

        return view('admin.offers.form', [
            'offer' => $offer,
            'products' => $products,
            'initialLines' => $initialLines,
        ]);
    }

    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_de' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_de' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'bundle_price' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $data = collect($validated)->except(['lines', 'image'])->toArray();
            if ($request->hasFile('image')) {
                if ($offer->image) {
                    Storage::disk('public')->delete($offer->image);
                }
                $data['image'] = $request->file('image')->store('offers', 'public');
            }
            $data['is_active'] = $request->boolean('is_active', true);
            $data['sort_order'] = $data['sort_order'] ?? 0;

            $offer->update($data);

            $offer->products()->sync($this->mergeOfferLines($validated['lines']));

            DB::commit();

            return redirect()
                ->route('admin.offers.index')
                ->with('success', __('Offer updated successfully.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Offer $offer)
    {
        if ($offer->image) {
            Storage::disk('public')->delete($offer->image);
        }
        $offer->delete();

        return redirect()
            ->route('admin.offers.index')
            ->with('success', __('Offer deleted.'));
    }

    public function toggle(Offer $offer)
    {
        $offer->update(['is_active' => ! $offer->is_active]);

        return back()->with('success', __('Offer status updated.'));
    }

    /**
     * @param  array<int, array{product_id: string|int, quantity: int|string}>  $lines
     * @return array<int, array{quantity: int, sort_order: int}>
     */
    private function mergeOfferLines(array $lines): array
    {
        $sync = [];
        foreach ($lines as $index => $line) {
            $pid = (int) $line['product_id'];
            if (! array_key_exists($pid, $sync)) {
                $sync[$pid] = [
                    'quantity' => 0,
                    'sort_order' => $index,
                ];
            }
            $sync[$pid]['quantity'] += (int) $line['quantity'];
        }

        return $sync;
    }
}
