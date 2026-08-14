<?php

namespace Database\Factories;

use App\Models\Movimentacao;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovimentacaoFactory extends Factory
{
    protected $model = Movimentacao::class;

    public function definition(): array
    {
        $tipo = $this->faker->randomElement(['entrada', 'saida']);
        $quantidade = $this->faker->numberBetween(1, 20);

        return [
            'produto_id' => Produto::inRandomOrder()->first()?->id ?? Produto::factory(),
            'tipo' => $tipo,
            'quantidade' => $quantidade,
            'observacao' => $this->faker->optional(0.7)->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }
}
