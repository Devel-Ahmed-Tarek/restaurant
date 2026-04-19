<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTopping extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'name_de',
        'price',
        'is_required',
        'max_quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_required' => 'boolean',
    ];

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
}
