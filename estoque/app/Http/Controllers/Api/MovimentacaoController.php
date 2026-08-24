<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movimentacao;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class MovimentacaoController extends Controller
{
    public function index()
    {
        return DataTables::eloquent(Movimentacao::with('produto')->latest())
            ->addColumn('produto_nome', fn(Movimentacao $movimentacao) => $movimentacao->produto?->nome ?? 'Produto removido')
            ->toJson();
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->canCreateRecords(), 403);

        if ($request->has('tipo')) {
            $tipo = mb_strtolower(trim($request->tipo), 'UTF-8');

            $request->merge([
                'tipo' => $tipo === 'saída' ? 'saida' : $tipo
            ]);
        }

        $validated = $request->validate([
            'produto_id' => 'required|exists:produtos,id,ativo,1',
            'tipo'       => 'required|in:entrada,saida',
            'quantidade' => 'required|integer|min:1',
            'observacao' => 'nullable|string|max:255'
        ]);

        $tipoNormalizado = in_array(mb_strtolower($validated['tipo'], 'UTF-8'), ['saida', 'saída']) ? 'saida' : 'entrada';
        $validated['tipo'] = $tipoNormalizado;

        try {
            $movimentacao = DB::transaction(function () use ($validated, $tipoNormalizado) {
                $produto = Produto::where('id', $validated['produto_id'])
                    ->where('ativo', true)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($tipoNormalizado === 'saida') {
                    if ($produto->quantidade_estoque < $validated['quantidade']) {
                        throw ValidationException::withMessages([
                            'quantidade' => ["Estoque insuficiente para esta saída. Estoque atual: {$produto->quantidade_estoque}"]
                        ]);
                    }
                    $produto->quantidade_estoque -= $validated['quantidade'];
                } else {
                    $produto->quantidade_estoque += $validated['quantidade'];
                }

                $produto->updated_at = Carbon::now();
                $produto->save();

                return Movimentacao::create($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Movimentação registrada com sucesso!',
                'data'    => $movimentacao->load('produto')
            ], 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Erro ao registrar movimentação.', [
                'exception' => $e,
                'produto_id' => $validated['produto_id'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível registrar a movimentação.'
            ], 500);
        }
    }
}
