<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfissionalAgenda extends Model
{
    use HasFactory;
    protected $fillable = [
        'motivo',
        'procedimento_id',
        'profissional_id',
        'atendimento_id',
        'data',
        'hora_inicio',
        'hora_fim',
        'status',
        'disponivel'
    ];
}
