<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedimento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome_procedimento',
        'pacote_id',
        'descricao',
        'porcentagem_clinica'
    ];
}
