<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FornecedorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'razao_social' => fake()->company(),
            'nome_fantasia' => fake()->companySuffix(),
            'cnpj' => fake()->numerify('##.###.###/0001-##'),
            'email' => fake()->companyEmail(),
            'telefone' => fake()->phoneNumber(),
            'ativo' => true,
        ];
    }
}
