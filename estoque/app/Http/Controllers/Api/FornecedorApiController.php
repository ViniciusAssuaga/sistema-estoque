<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FornecedorApiController extends Controller
{
    // GET /api/fornecedores
    public function index(Request $request)
    {
        // Se for requisição da tabela (DataTables/AJAX)
        if ($request->ajax()) {
            $data = Fornecedor::select(['id', 'razao_social', 'nome_fantasia', 'cnpj', 'email', 'telefone']);

            return DataTables::of($data)
                ->addColumn('acoes', function($row){
                    return '
                        <button type="button" class="btn btn-sm btn-outline-laravel py-0 px-2 btn-editar" data-id="'.$row->id.'" title="Editar Fornecedor">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-laravel py-0 px-2 btn-excluir" data-id="'.$row->id.'" title="Excluir Fornecedor">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['acoes'])
                ->make(true);
        }

        // Retorno padrão da API em JSON caso seja chamado direto via GET /api/fornecedores
        return response()->json(Fornecedor::paginate(10));
    }

    // POST /api/fornecedores
    public function store(Request $request)
    {
        abort_unless($request->user()->canCreateRecords(), 403);

        $request->validate([
            'razao_social'  => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj'          => 'required|string|max:20|unique:fornecedores,cnpj',
            'email'         => 'nullable|email|max:255',
            'telefone'      => 'nullable|string|max:20',
        ]);

        $fornecedor = Fornecedor::create($request->all());

        return response()->json([
            'message' => 'Fornecedor cadastrado com sucesso!',
            'data'    => $fornecedor
        ], 201);
    }

    // GET /api/fornecedores/{id}/edit
    public function show($id)
    {
        $fornecedor = Fornecedor::findOrFail($id);
        return response()->json($fornecedor);
    }

    // PUT /api/fornecedores/{id}
    public function update(Request $request, $id)
    {
        abort_unless($request->user()->canEditRecords(), 403);

        $request->validate([
            'razao_social'  => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj'          => 'required|string|max:20|unique:fornecedores,cnpj,' . $id,
            'email'         => 'nullable|email|max:255',
            'telefone'      => 'nullable|string|max:20',
        ]);

        $fornecedor = Fornecedor::findOrFail($id);
        $fornecedor->update($request->all());

        return response()->json([
            'message' => 'Fornecedor atualizado com sucesso!'
        ]);
    }

    // DELETE /api/fornecedores/{id}
    public function destroy($id)
    {
        abort_unless(request()->user()->canDeleteRecords(), 403);

        $fornecedor = Fornecedor::findOrFail($id);
        $fornecedor->delete();

        return response()->json([
            'message' => 'Fornecedor excluído com sucesso!'
        ]);
    }
}
