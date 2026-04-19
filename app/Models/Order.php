<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'tracking_token',
        'customer_name',
        'customer_phone',
        'customer_address',
        'notes',
        'subtotal',
        'delivery_fee',
        'discount',
        'total',
        'status',
        'payment_method',
        'is_paid',
        'accepted_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'is_paid' => 'boolean',
        'accepted_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Status labels
     */
    public const STATUS_LABELS = [
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'preparing' => 'Preparing',
        'ready' => 'Ready',
        'on_the_way' => 'On the Way',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    /**
     * Status colors for UI
     */
    public const STATUS_COLORS = [
        'pending' => 'yellow',
        'accepted' => 'blue',
        'preparing' => 'indigo',
        'ready' => 'purple',
        'on_the_way' => 'orange',
        'delivered' => 'green',
        'cancelled' => 'red',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = static::generateOrderNumber();
            }
            if (!$order->tracking_token) {
                $order->tracking_token = Str::random(32);
            }
        });
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        
        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Get order items
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        $key = self::STATUS_LABELS[$this->status] ?? $this->status;

        return __($key);
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'gray';
    }

    /**
     * Scope for today's orders
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for active orders (not delivered or cancelled)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['delivered', 'cancelled']);
    }

    /**
     * Get WhatsApp link for order tracking
     */
    public function getWhatsAppLinkAttribute(): string
    {
        $phone = site_setting('whatsapp_number', '') ?: '';
        $message = urlencode(__('WhatsApp track order message', ['order' => $this->order_number]));
        return "https://wa.me/{$phone}?text={$message}";
    }

    /**
     * Get tracking URL (includes current locale)
     */
    public function getTrackingUrlAttribute(): string
    {
        return route('orders.track.show', [
            'locale' => app()->getLocale(),
            'token' => $this->tracking_token,
        ]);
    }
}
