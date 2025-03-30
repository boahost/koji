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
        'amount',
        'commission_rate',
        'status',
        'payment_data'
    ];

    protected $casts = [
        'payment_data' => 'array',
        'amount' => 'decimal:2'
    ];

    /**
     * Relacionamento com o revendedor
     */
    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    /**
     * Relacionamento com o pedido
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
