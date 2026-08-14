<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class ClienteFactory extends Factory
{
    public function definition(): array
    {
        $faker = FakerFactory::create('pt_BR');

        return [
            'nome' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'telefone' => $faker->phoneNumber(),
            'cpf_cnpj' => $faker->unique()->numerify('###.###.###-##'),
            'endereco' => $faker->address(),
        ];
    }
}
