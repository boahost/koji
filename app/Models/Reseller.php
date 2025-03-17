<?php

namespace App\Models;

use App\Traits\DocumentValidation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;

class Reseller extends Authenticatable
{
    use HasFactory, SoftDeletes, DocumentValidation, Notifiable;

    protected $fillable = [
        'name',
        'document',
        'email',
        'password',
        'commission_rate'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Automatically hash the password when setting
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /**
     * Format the document when getting
     */
    public function getDocumentAttribute($value)
    {
        return $this->formatDocument($value);
    }

    /**
     * Remove formatting when setting the document
     */
    public function setDocumentAttribute($value)
    {
        $this->attributes['document'] = preg_replace('/[^0-9]/', '', $value);
    }
}
