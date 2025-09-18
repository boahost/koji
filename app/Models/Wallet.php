<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'wallets';
    protected $fillable = [
        'customer_id',
        'order_id',
        'valor',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
} 