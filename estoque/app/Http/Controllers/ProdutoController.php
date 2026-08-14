<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria; // <-- 1. Importar a model de Categoria
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $produtos = Produto::with('categoria');

            // <-- Adicionado o filtro por categoria no Server-Side
            if ($request->filled('categoria_id')) {
                $produtos->where('categoria_id', $request->categoria_id);
            }

            return DataTables::of($produtos)
                ->addColumn('categoria_nome', function ($row) {
                    return $row->categoria ? $row->categoria->nome : '-';
                })
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

        $categorias = Categoria::orderBy('nome')->get();
        return view('produtos.index', compact('categorias'));
    }

    public function create()
    {
        return redirect()->route('produtos.index');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sku'                  => 'required|string|max:50|unique:produtos,sku',
            'nome'                 => 'required|string|max:255',
            'categoria_id'         => 'required|exists:categorias,id', // <-- Validação da categoria
            'preco_custo'          => 'required|string',
            'preco_venda'          => 'required|string',
            'quantidade_estoque' => 'required|integer|min:0',
            'estoque_minimo'     => 'nullable|integer|min:0',
            'descricao'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['preco_custo'] = $this->converterPrecoParaFloat($data['preco_custo']);
            $data['preco_venda'] = $this->converterPrecoParaFloat($data['preco_venda']);
            $data['ativo']       = $request->has('ativo');

            $produto = Produto::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Produto cadastrado com sucesso!',
                'data'    => $produto
            ], 201);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Erro ao salvar no banco de dados. Verifique os dados enviados.'
            ], 400);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro interno ao processar o cadastro do produto.'
            ], 500);
        }
    }

    public function edit(Produto $produto): JsonResponse
    {
        return response()->json($produto);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sku'                  => ['required', 'string', 'max:50', Rule::unique('produtos', 'sku')->ignore($id)],
            'nome'                 => 'required|string|max:255',
            'categoria_id'         => 'required|exists:categorias,id', // <-- Validação da categoria
            'preco_custo'          => 'required|string',
            'preco_venda'          => 'required|string',
            'quantidade_estoque' => 'required|integer|min:0',
            'estoque_minimo'     => 'nullable|integer|min:0',
            'descricao'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $produto = Produto::findOrFail($id);

            $data = $validator->validated();
            $data['preco_custo'] = $this->converterPrecoParaFloat($data['preco_custo']);
            $data['preco_venda'] = $this->converterPrecoParaFloat($data['preco_venda']);
            $data['ativo']       = $request->has('ativo');

            $produto->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Produto atualizado com sucesso!'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Produto não encontrado para atualização.'
            ], 404);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Erro ao atualizar no banco de dados.'
            ], 400);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro interno ao processar a alteração do produto.'
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $produto = Produto::findOrFail($id);
            $produto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produto removido com sucesso!'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Produto não encontrado para exclusão.'
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Não foi possível excluir o produto.'
            ], 500);
        }
    }

    private function converterPrecoParaFloat($valor): float
    {
        if (is_numeric($valor)) {
            return (float) $valor;
        }

        $valorLimpo = str_replace('.', '', $valor);
        $valorLimpo = str_replace(',', '.', $valorLimpo);

        return (float) $valorLimpo;
    }

    public function listarJson()
    {
        // Retorna apenas os campos necessários para o autocomplete
        $produtos = Produto::select('id', 'nome', 'quantidade_estoque')->get();
        return response()->json($produtos);
    }
}
