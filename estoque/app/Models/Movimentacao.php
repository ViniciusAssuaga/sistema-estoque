<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimentacao extends Model
{
    use HasFactory;

    protected $table = 'movimentacoes';

    protected $fillable = [
        'produto_id',
        'tipo',
        'quantidade',
        'observacao'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
