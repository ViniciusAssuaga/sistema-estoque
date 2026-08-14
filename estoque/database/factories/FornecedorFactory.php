<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class FornecedorFactory extends Factory
{
    public function definition(): array
    {
        $faker = FakerFactory::create('pt_BR');

        return [
            'razao_social' => $faker->company(),
            'nome_fantasia' => $faker->companySuffix(),
            'cnpj' => $faker->numerify('##.###.###/0001-##'),
            'email' => $faker->companyEmail(),
            'telefone' => $faker->phoneNumber(),
            'ativo' => true,
        ];
    }
}
