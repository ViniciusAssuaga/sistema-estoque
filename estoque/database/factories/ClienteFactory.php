<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'telefone' => fake()->phoneNumber(),
            'cpf_cnpj' => fake()->unique()->numerify('###.###.###-##'),
            'endereco' => fake()->address(),
        ];
    }
}
