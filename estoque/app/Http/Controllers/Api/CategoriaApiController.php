<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class CategoriaApiController extends Controller
{
    // GET /api/categorias (Listagem via AJAX/DataTables ou JSON direto)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Categoria::select(['id', 'nome', 'descricao']);

            return DataTables::of($data)
                ->addColumn('acoes', function($row){
                    return '
                        <button type="button" class="btn btn-sm btn-outline-laravel py-0 px-2 btn-editar" data-id="'.$row->id.'" title="Editar Categoria">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-laravel py-0 px-2 btn-excluir" data-id="'.$row->id.'" title="Excluir Categoria">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['acoes'])
                ->make(true);
        }

        return response()->json(Categoria::paginate(10));
    }

    // POST /api/categorias (Criar)
    public function store(Request $request)
    {
        abort_unless($request->user()->canCreateRecords(), 403);

        $validator = Validator::make($request->all(), [
            'nome'      => 'required|string|max:255|unique:categorias,nome',
            'descricao' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $categoria = Categoria::create($request->all());

            return response()->json([
                'message' => 'Categoria cadastrada com sucesso!',
                'data'    => $categoria
            ], 201);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Erro ao salvar no banco de dados.'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro interno ao processar o cadastro.'
            ], 500);
        }
    }

    // GET /api/categorias/{id} (Exibir detalhes para edição)
    public function show($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            return response()->json($categoria);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Categoria não encontrada.'
            ], 404);
        }
    }

    // PUT /api/categorias/{id} (Atualizar)
    public function update(Request $request, $id)
    {
        abort_unless($request->user()->canEditRecords(), 403);

        $validator = Validator::make($request->all(), [
            'nome'      => ['required', 'string', 'max:255', Rule::unique('categorias', 'nome')->ignore($id)],
            'descricao' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $categoria = Categoria::findOrFail($id);
            $categoria->update($request->all());

            return response()->json([
                'message' => 'Categoria atualizada com sucesso!'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Categoria não encontrada para atualização.'
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Erro ao atualizar no banco de dados.'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro interno ao processar a requisição.'
            ], 500);
        }
    }

    // DELETE /api/categorias/{id} (Excluir)
    public function destroy($id)
    {
        abort_unless(request()->user()->canDeleteRecords(), 403);

        try {
            $categoria = Categoria::findOrFail($id);
            $categoria->delete();

            return response()->json([
                'message' => 'Categoria excluída com sucesso!'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Categoria não encontrada para exclusão.'
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Não é possível excluir esta categoria pois ela possui produtos vinculados.'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro ao tentar excluir a categoria.'
            ], 500);
        }
    }
}
