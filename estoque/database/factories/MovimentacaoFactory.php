<?php

namespace Database\Factories;

use App\Models\Movimentacao;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class MovimentacaoFactory extends Factory
{
    protected $model = Movimentacao::class;

    public function definition(): array
    {
        $faker = FakerFactory::create('pt_BR');

        $tipo = $faker->randomElement(['entrada', 'saida']);
        $quantidade = $faker->numberBetween(1, 20);

        return [
            'produto_id' => Produto::inRandomOrder()->first()?->id ?? Produto::factory(),
            'tipo' => $tipo,
            'quantidade' => $quantidade,
            'observacao' => $faker->optional(0.7)->sentence(),
            'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }
}
