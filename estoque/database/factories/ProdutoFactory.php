<?php

namespace Database\Factories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{
    protected $model = Produto::class;

    public function definition(): array
    {
        $precoCusto = fake()->randomFloat(2, 5, 500);
        $precoVenda = $precoCusto * fake()->randomFloat(2, 1.2, 2.5); // Margem de lucro realista

        return [
            'sku' => strtoupper(fake()->unique()->bothify('PROD-????-#####')),
            'nome' => ucfirst(fake()->words(3, true)),
            'descricao' => fake()->sentence(10),
            'preco_custo' => $precoCusto,
            'preco_venda' => round($precoVenda, 2),
            'quantidade_estoque' => fake()->numberBetween(0, 500),
            'estoque_minimo' => fake()->numberBetween(5, 20),
            'ativo' => fake()->boolean(85), // 85% de chance de vir como ativo
        ];
    }
}
