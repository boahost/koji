<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyQuery extends Model
{
    protected $table = 'property_queries';
    protected $fillable = [
        'customer_id',
        'codigo_imovel',
        'ni_consultado',
        'resposta_api',
        'tipo_consulta',
    ];
    protected $casts = [
        'resposta_api' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
} 