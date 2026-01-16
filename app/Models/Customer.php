<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use App\Notifications\CustomerResetPasswordNotification;

class Customer extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $table = 'customers';

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
            if ($customer->password && !Hash::isHashed($customer->password)) {
                $customer->password = Hash::make($customer->password);
            }
        });

        static::updating(function ($customer) {
            if ($customer->isDirty('password') && $customer->password && !Hash::isHashed($customer->password)) {
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

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function getBalanceAttribute()
    {
        return $this->wallets()->sum('valor');
    }

    public function propertyQueries()
    {
        return $this->hasMany(PropertyQuery::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerResetPasswordNotification($token));
    }
}
