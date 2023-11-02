<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioTrabalho extends Model
{
    use HasFactory;

    protected $table = 'horarios_trabalho';
    protected $fillable = [
        'user_id',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
    ];
}
