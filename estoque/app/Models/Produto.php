<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use HasFactory, SoftDeletes;

    // Liberando os campos para atribuição em massa (Mass Assignment)
    protected $fillable = [
        'sku',
        'nome',
        'descricao',
        'preco_custo',
        'preco_venda',
        'quantidade_estoque',
        'estoque_minimo',
        'ativo',
    ];
}

?>
