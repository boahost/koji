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
        'commission_rate',
        'reference_code'
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

    /**
     * Gera um código de referência único para o revendedor
     */
    public function generateReferenceCode()
    {
        if (!$this->reference_code) {
            $this->reference_code = strtoupper(substr(md5($this->id . $this->email . time()), 0, 8));
            $this->save();
        }
        
        return $this->reference_code;
    }

    /**
     * Retorna a URL completa de referência do revendedor
     */
    public function getReferralUrl()
    {
        if (!$this->reference_code) {
            $this->generateReferenceCode();
        }
        
        return url('/produtos?ref=' . $this->reference_code);
    }
    
    /**
     * Relacionamento com as comissões do revendedor
     */
    public function commissions()
    {
        return $this->hasMany(ResellerCommission::class);
    }
    
    /**
     * Relacionamento com os itens do carrinho associados ao revendedor
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    
    /**
     * Obter o total de comissões pendentes
     */
    public function getPendingCommissionsTotal()
    {
        return $this->commissions()
            ->where('status', 'pending')
            ->sum('commission_amount');
    }
    
    /**
     * Obter o total de comissões aprovadas
     */
    public function getApprovedCommissionsTotal()
    {
        return $this->commissions()
            ->where('status', 'approved')
            ->sum('commission_amount');
    }
    
    /**
     * Obter o total de comissões pagas
     */
    public function getPaidCommissionsTotal()
    {
        return $this->commissions()
            ->where('status', 'paid')
            ->sum('commission_amount');
    }
}
