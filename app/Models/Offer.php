<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Offer extends Model
{
    protected $fillable = [
        'name',
        'name_de',
        'description',
        'description_de',
        'image',
        'bundle_price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'bundle_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['quantity', 'sort_order'])
            ->orderByPivot('sort_order')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'de' && $this->name_de) {
            return $this->name_de;
        }

        return $this->name;
    }

    public function getDisplayDescriptionAttribute(): ?string
    {
        if (app()->getLocale() === 'de' && $this->description_de) {
            return $this->description_de;
        }

        return $this->description;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return Storage::url($this->image);
    }

    /**
     * Sum of individual product base prices (reference only, not charged).
     */
    public function getReferenceTotalAttribute(): float
    {
        $this->loadMissing('products');

        $sum = 0;
        foreach ($this->products as $product) {
            $sum += (float) $product->base_price * (int) $product->pivot->quantity;
        }

        return round($sum, 2);
    }
}
