<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acesso extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'user_id',
        'user_agent',
        'total_acessos',
        'ultimo_acesso',
    ];

    protected $casts = [
        'ultimo_acesso' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
