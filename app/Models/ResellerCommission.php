<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id',
        'order_id',
        'order_item_id',
        'product_id',
        'product_price',
        'quantity',
        'commission_rate',
        'commission_amount',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the reseller that owns the commission.
     */
    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    /**
     * Get the order that owns the commission.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the order item that owns the commission.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the product that owns the commission.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
