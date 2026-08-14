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
        $validated = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'tipo' => 'required|in:entrada,saida',
            'quantidade' => 'required|integer|min:1',
            'observacao' => 'nullable|string|max:255'
        ]);

        try {
            $resultado = DB::transaction(function () use ($validated) {
                $movimentacao = Movimentacao::create($validated);

                $produto = Produto::findOrFail($validated['produto_id']);
                
                if ($validated['tipo'] === 'entrada') {
                    $produto->quantidade_estoque += $validated['quantidade'];
                } else {
                    if ($produto->quantidade_estoque < $validated['quantidade']) {
                        throw new \Exception('Estoque insuficiente para esta saída.');
                    }
                    $produto->quantidade_estoque -= $validated['quantidade'];
                }
                
                $produto->save();

                return $movimentacao->load('produto');
            });

            return response()->json([
                'success' => true,
                'message' => 'Movimentação registrada com sucesso!',
                'data' => $resultado
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
