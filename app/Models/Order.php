<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Customer;
use App\Models\ShippingMethod;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Reseller;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'reseller_id',
        'shipping_method_id',
        'subtotal',
        'shipping_cost',
        'total',
        'status',
        'notes',
        'address',
        'city',
        'state',
        'zip_code',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the reseller associated with the order, if any.
     */
    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    /**
     * Get the shipping method for the order.
     */
    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payments for the order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the payment for the order.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
