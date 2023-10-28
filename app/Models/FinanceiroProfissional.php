<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceiroProfissional extends Model
{
    use HasFactory;
    protected $fillable = [
        'atendimento_id',
        'value',
        'receipt',
    ];
}
