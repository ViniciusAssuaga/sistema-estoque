<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movimentacao;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        // Normaliza o tipo enviando para minúsculas
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
            // Utiliza o DB::transaction manual para garantir Rollback imediato no Postgres/NeonDB
            DB::beginTransaction();

            // 1. Busca o produto sem lockForUpdate (compatível com PgBouncer/Neon Pooler)
            $produto = Produto::where('id', $validated['produto_id'])->firstOrFail();

            // 2. Valida se há estoque suficiente
            if ($tipoNormalizado === 'saida') {
                if ($produto->quantidade_estoque < $validated['quantidade']) {
                    throw new \Exception("Estoque insuficiente para esta saída. Estoque atual: {$produto->quantidade_estoque}");
                }
                $novoEstoque = $produto->quantidade_estoque - $validated['quantidade'];
            } else {
                $novoEstoque = $produto->quantidade_estoque + $validated['quantidade'];
            }

            // 3. Atualiza usando o Query Builder puro para evitar incompatibilidade de formato de data no Eloquent/Postgres
            DB::table('produtos')
                ->where('id', $produto->id)
                ->update([
                    'quantidade_estoque' => $novoEstoque,
                    'updated_at'         => now()->toDateTimeString()
                ]);

            // 4. Cria a movimentação
            $movimentacao = Movimentacao::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Movimentação registrada com sucesso!',
                'data'    => $movimentacao->load('produto')
            ], 201);

        } catch (Throwable $e) {
            // Cancela a transação imediatamente para liberar o pooler do NeonDB
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
