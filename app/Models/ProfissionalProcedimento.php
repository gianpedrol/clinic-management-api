<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfissionalProcedimento extends Model
{
    use HasFactory;

    protected $table = 'profissional_procedimentos';

    protected $fillable = [
        'user_id',
        'procedimento_id',
        'price'
    ];
}
