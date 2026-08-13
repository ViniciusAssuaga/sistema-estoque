<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FornecedorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'razao_social' => $this->faker->company(),
            'nome_fantasia' => $this->faker->companySuffix(),
            'cnpj' => $this->faker->numerify('##.###.###/0001-##'),
            'email' => $this->faker->companyEmail(),
            'telefone' => $this->faker->phoneNumber(),
            'ativo' => true,
        ];
    }
}
