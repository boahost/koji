<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingMethod extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'value',
        'active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'active' => 'boolean'
    ];
}
