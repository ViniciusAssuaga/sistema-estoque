<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Gera 10.000 registros no banco de dados
        Produto::factory()->count(10000)->create();
    }
}
