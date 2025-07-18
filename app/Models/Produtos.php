<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produtos extends Model
{
    use HasFactory;

    protected $table = 'produtos';
    protected $fillable = [
        'nome',
        'preco',
        'estoque',
    ];

    public function estoques()
    {
        return $this->hasMany(\App\Models\Estoque::class, 'produto_id');
    }
}
