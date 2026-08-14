<?php

namespace Database\Factories;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class ProdutoFactory extends Factory
{
    protected $model = Produto::class;

    public function definition(): array
    {
        $faker = FakerFactory::create('pt_BR');

        $precoCusto = $faker->randomFloat(2, 5, 500);
        $precoVenda = $precoCusto * $faker->randomFloat(2, 1.2, 2.5);

        return [
            'sku' => strtoupper($faker->unique()->bothify('PROD-????-#####')),
            'nome' => ucfirst($faker->words(3, true)),
            'categoria_id' => Categoria::inRandomOrder()->first()?->id ?? Categoria::factory(),
            'descricao' => $faker->sentence(10),
            'preco_custo' => $precoCusto,
            'preco_venda' => round($precoVenda, 2),
            'quantidade_estoque' => $faker->numberBetween(0, 500),
            'estoque_minimo' => $faker->numberBetween(5, 20),
            'ativo' => $faker->boolean(85),
        ];
    }
}
