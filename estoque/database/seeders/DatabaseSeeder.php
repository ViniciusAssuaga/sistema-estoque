<?php

namespace Database\Seeders;

use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Models\Categoria;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
         // Gera 15 registros de categorias de teste
        Categoria::factory()->count(15)->create();

        // Gera 10.000 registros de produtos
        Produto::factory()->count(10000)->create();

        // Gera 50 registros de clientes de teste
        Cliente::factory()->count(50)->create();

        // Gera 50 registros de fornecedores de teste
        Fornecedor::factory()->count(50)->create();
    }
}
