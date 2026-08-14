<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;
use App\Models\Movimentacao;
use Illuminate\Support\Facades\DB;

class MovimentacaoSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que existem produtos cadastrados antes de gerar movimentações
        $produtos = Produto::all();

        if ($produtos->isEmpty()) {
            $this->command->info('Nenhum produto encontrado. Cadastre produtos antes de rodar o seeder de movimentações.');
            return;
        }

        DB::transaction(function () use ($produtos) {
            for ($i = 0; $i < 100; $i++) {
                $produto = $produtos->random();
                $tipo = rand(0, 1) ? 'entrada' : 'saida';
                $quantidade = rand(1, 15);

                // Se for saída, garante que não deixe o estoque negativo (ou ajusta a quantidade)
                if ($tipo === 'saida') {
                    if ($produto->quantidade_estoque < $quantidade) {
                        $tipo = 'entrada'; // Transforma em entrada se o estoque for menor
                        $produto->quantidade_estoque += $quantidade;
                    } else {
                        $produto->quantidade_estoque -= $quantidade;
                    }
                } else {
                    $produto->quantidade_estoque += $quantidade;
                }

                $produto->save();

                Movimentacao::create([
                    'produto_id' => $produto->id,
                    'tipo' => $tipo,
                    'quantidade' => $quantidade,
                    'observacao' => 'Movimentação gerada via Seeder (' . ucfirst($tipo) . ')',
                    'created_at' => now()->subMinutes(rand(1, 10000)), // Datas distribuídas no passado
                    'updated_at' => now(),
                ]);
            }
        });

        $this->command->info('100 movimentações criadas com sucesso!');
    }
}
