<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedimentoAtendimento extends Model
{
    use HasFactory;
    protected $fillable = [
        'atendimento_id',
        'procedimento_id',
        'profissional_id',
        'data',
        'hora_inicio',
        'hora_fim',
        'status',
        'valor_procedimento_profissional'
    ];
}
