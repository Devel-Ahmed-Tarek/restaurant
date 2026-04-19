<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'name_de',
        'description',
        'description_de',
        'image',
        'base_price',
        'old_price',
        'is_available',
        'is_featured',
        'tags',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'tags' => 'array',
    ];

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get product sizes
     */
    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class)->orderBy('sort_order');
    }

    /**
     * Get product toppings
     */
    public function toppings(): HasMany
    {
        return $this->hasMany(ProductTopping::class);
    }

    /**
     * Scope for available products
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        
        return Storage::url($this->image);
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'de' && $this->name_de) {
            return $this->name_de;
        }
        return $this->name;
    }

    /**
     * Get display description
     */
    public function getDisplayDescriptionAttribute(): ?string
    {
        if (app()->getLocale() === 'de' && $this->description_de) {
            return $this->description_de;
        }
        return $this->description;
    }

    /**
     * Check if product has discount
     */
    public function hasDiscount(): bool
    {
        return $this->old_price && $this->old_price > $this->base_price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->hasDiscount()) {
            return 0;
        }
        return round((($this->old_price - $this->base_price) / $this->old_price) * 100);
    }
}
