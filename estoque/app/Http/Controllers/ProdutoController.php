<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $produtos = Produto::query();

            return DataTables::of($produtos)
                // Usando addColumn pois são colunas virtuais/formatadas
                ->addColumn('preco_custo_formatted', function ($row) {
                    return 'R$ ' . number_format($row->preco_custo, 2, ',', '.');
                })
                ->addColumn('preco_venda_formatted', function ($row) {
                    return 'R$ ' . number_format($row->preco_venda, 2, ',', '.');
                })
                ->addColumn('estoque_badge', function ($row) {
                    $badgeClass = $row->quantidade_estoque <= $row->estoque_minimo ? 'bg-danger' : 'bg-secondary';
                    return '<span class="badge ' . $badgeClass . '">' . $row->quantidade_estoque . ' un</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    return $row->ativo 
                        ? '<span class="badge bg-success">Ativo</span>' 
                        : '<span class="badge bg-danger">Inativo</span>';
                })
                ->addColumn('acoes', function ($row) {
                    return '
                        <div class="d-flex gap-1 justify-content-center">
                            <button class="btn btn-sm btn-outline-laravel btn-editar" data-id="' . $row->id . '" title="Editar Produto">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-excluir" data-id="' . $row->id . '" title="Excluir Produto">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['estoque_badge', 'status_badge', 'acoes'])
                ->make(true);
        }

        return view('produtos.index');
    }

    public function create()
    {
        return redirect()->route('produtos.index');
    }

    public function store(StoreProdutoRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Converte os preços de "1.250,50" para "1250.50" (float válido para o banco)
        $validated['preco_custo'] = $this->converterPrecoParaFloat($validated['preco_custo']);
        $validated['preco_venda'] = $this->converterPrecoParaFloat($validated['preco_venda']);
        $validated['ativo'] = $request->has('ativo');

        $produto = Produto::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produto cadastrado com sucesso!',
            'data' => $produto
        ], 201);
    }

    public function edit(Produto $produto): JsonResponse
    {
        return response()->json($produto);
    }

    public function update(UpdateProdutoRequest $request, Produto $produto): JsonResponse
    {
        $validated = $request->validated();

        // Converte os preços de "1.250,50" para "1250.50" (float válido para o banco)
        $validated['preco_custo'] = $this->converterPrecoParaFloat($validated['preco_custo']);
        $validated['preco_venda'] = $this->converterPrecoParaFloat($validated['preco_venda']);
        $validated['ativo'] = $request->has('ativo');

        $produto->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produto atualizado com sucesso!'
        ]);
    }

    public function destroy(Produto $produto): JsonResponse
    {
        $produto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produto removido com sucesso!'
        ]);
    }

    /**
     * Auxiliar para converter "1.500,00" para "1500.00"
     */
    private function converterPrecoParaFloat($valor): float
    {
        if (is_numeric($valor)) {
            return (float) $valor;
        }

        $valorLimpo = str_replace('.', '', $valor);
        $valorLimpo = str_replace(',', '.', $valorLimpo);

        return (float) $valorLimpo;
    }
}
