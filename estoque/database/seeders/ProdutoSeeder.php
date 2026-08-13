<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $total = 10000;
        $chunkSize = 2000; // Insere de 2000 em 2000 por vez

        for ($i = 0; $i < $total; $i += $chunkSize) {
            // Gera os dados em memória usando a Factory sem salvar no banco ainda (.make())
            $data = Produto::factory()->count($chunkSize)->make()->toArray();
            
            // Faz um único INSERT no banco para os 2000 registros
            Produto::insert($data);
        }
    }
}
