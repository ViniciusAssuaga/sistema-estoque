<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movimentacao;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimentacaoController extends Controller
{
    public function index()
    {
        $movimentacoes = Movimentacao::with('produto')->latest()->get();
        return response()->json($movimentacoes, 200);
    }

    public function store(Request $request)
    {
        // Tratamento para aceitar 'saida'/'entrada' tanto em minúsculas quanto maiúsculas (ex: Saída/Entrada)
        if ($request->has('tipo')) {
            $request->merge([
                'tipo' => mb_strtolower($request->tipo, 'UTF-8')
            ]);
        }

        $validated = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'tipo'       => 'required|in:entrada,saida,Saída,Entrada',
            'quantidade' => 'required|integer|min:1',
            'observacao' => 'nullable|string|max:255'
        ]);

        // Normaliza o tipo para salvar de forma padronizada
        $tipoNormalizado = in_array(mb_strtolower($validated['tipo'], 'UTF-8'), ['saida', 'saída']) ? 'saida' : 'entrada';
        $validated['tipo'] = $tipoNormalizado;

        try {
            $resultado = DB::transaction(function () use ($validated, $tipoNormalizado) {
                // 1. Busca primeiro o produto e trava a linha para atualização (lockForUpdate evita race condition)
                $produto = Produto::where('id', $validated['produto_id'])->lockForUpdate()->firstOrFail();

                // 2. Valida se há estoque suficiente ANTES de executar qualquer INSERT na transação
                if ($tipoNormalizado === 'saida') {
                    if ($produto->quantidade_estoque < $validated['quantidade']) {
                        throw new \Exception("Estoque insuficiente para esta saída. Estoque atual: {$produto->quantidade_estoque}");
                    }
                    $produto->quantidade_estoque -= $validated['quantidade'];
                } else {
                    $produto->quantidade_estoque += $validated['quantidade'];
                }

                // 3. Atualiza o estoque do produto
                $produto->save();

                // 4. Registra a movimentação após a garantia de que o estoque é válido
                $movimentacao = Movimentacao::create($validated);

                return $movimentacao->load('produto');
            });

            return response()->json([
                'success' => true,
                'message' => 'Movimentação registrada com sucesso!',
                'data'    => $resultado
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
