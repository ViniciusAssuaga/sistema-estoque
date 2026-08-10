<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            DB::table('produtos')->insert([
                'sku' => 'PROD-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nome' => 'Produto de Teste ' . $i,
                'descricao' => 'Descrição do produto corporativo de teste ' . $i,
                'preco_custo' => rand(10, 100) + (rand(0, 99) / 100),
                'preco_venda' => rand(150, 500) + (rand(0, 99) / 100),
                'quantidade_estoque' => rand(5, 50),
                'estoque_minimo' => 5,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

?>
