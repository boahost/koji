<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class Customer extends Authenticatable
{
    use SoftDeletes;

    use Notifiable;

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
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
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

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
