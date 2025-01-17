<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceiroAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'atendimento_id',
        'value_atendimento',
        'value_clinica',
        'receipt',
    ];
}
