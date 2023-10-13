<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'servico_id',
        'profissional_id',
        'convenio_id',
        'data',
        'hora',
        'status',
        'metodo_pagamento',
        'descricao',
        'preco_estimado',
        'discount',
        'preco_total',
    ];
}
