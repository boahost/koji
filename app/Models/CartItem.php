<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'customer_id',
        'product_id',
        'quantity',
        'price',
        'reseller_id'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }
}
