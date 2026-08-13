<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    use HasFactory;

    // Força o Eloquent a usar a tabela correta no plural em português
    protected $table = 'fornecedores';

    protected $fillable = [
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'email',
        'telefone',
        'ativo',
    ];
}
