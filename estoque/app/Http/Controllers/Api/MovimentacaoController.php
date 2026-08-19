<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movimentacao;
use App\Models\Produto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Throwable;

class MovimentacaoController extends Controller
{
    public function index()
    {
        $movimentacoes = Movimentacao::with('produto')->latest()->get();
        return response()->json($movimentacoes, 200);
    }

    public function store(Request $request)
    {
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

        $tipoNormalizado = in_array(mb_strtolower($validated['tipo'], 'UTF-8'), ['saida', 'saída']) ? 'saida' : 'entrada';
        $validated['tipo'] = $tipoNormalizado;

        try {
            // 1. Busca o produto via Eloquent
            $produto = Produto::where('id', $validated['produto_id'])->firstOrFail();

            // 2. Valida estoque antes de qualquer alteração no banco
            if ($tipoNormalizado === 'saida') {
                if ($produto->quantidade_estoque < $validated['quantidade']) {
                    throw new \Exception("Estoque insuficiente para esta saída. Estoque atual: {$produto->quantidade_estoque}");
                }
                $produto->quantidade_estoque -= $validated['quantidade'];
            } else {
                $produto->quantidade_estoque += $validated['quantidade'];
            }

            // 3. Atualiza timestamps usando Carbon para o Postgres colocar aspas na data
            $produto->updated_at = Carbon::now();
            $produto->save();

            // 4. Salva a movimentação
            $movimentacao = Movimentacao::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Movimentação registrada com sucesso!',
                'data'    => $movimentacao->load('produto')
            ], 201);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
