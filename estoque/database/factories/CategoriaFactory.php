<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory; // <--- Importa o Faker oficial

class CategoriaFactory extends Factory
{
    public function definition(): array
    {
        $faker = FakerFactory::create('pt_BR'); // <--- Cria a instância manualmente (opcionalmente em português!)

        return [
            'nome'      => $faker->unique()->word(),
            'descricao' => $faker->sentence(),
        ];
    }
}
