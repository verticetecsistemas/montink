<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupons extends Model
{
    use HasFactory;
    protected $table = 'cupons';
    protected $fillable = [
        'nome',
        'desconto',
        'data_inicio',
        'data_fim',
        'ativo', 
        'user_id' // Adicionando o campo user_id para associar cupons a usuários
    ];
}
