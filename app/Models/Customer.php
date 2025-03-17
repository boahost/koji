<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'document',
        'password',
        'cep',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state'
    ];

    protected $hidden = [
        'password',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if ($customer->password) {
                $customer->password = Hash::make($customer->password);
            }
        });

        static::updating(function ($customer) {
            if ($customer->isDirty('password') && $customer->password) {
                $customer->password = Hash::make($customer->password);
            }
        });
    }
}
