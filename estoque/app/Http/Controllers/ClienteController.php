<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Cliente::select(['id', 'nome', 'email', 'telefone', 'cpf_cnpj']);
            
            return DataTables::of($data)
                ->addColumn('acoes', function($row){
                    return '
                        <button type="button" class="btn btn-sm btn-outline-laravel py-0 px-2 btn-editar" data-id="'.$row->id.'" title="Editar Cliente">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-laravel py-0 px-2 btn-excluir" data-id="'.$row->id.'" title="Excluir Cliente">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['acoes'])
                ->make(true);
        }

        return view('clientes.index');
    }

    /**
     * Edit - Buscar dados para preencher o modal
     */
    public function edit($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            return response()->json($cliente);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cliente não encontrado para edição.'
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar os dados do cliente.'
            ], 500);
        }
    }

    /**
     * Store - Cadastrar novo cliente
     */
    public function store(Request $request)
    {
        abort_unless($request->user()->canCreateRecords(), 403);

        // 1. Validação de formulário
        $validator = Validator::make($request->all(), [
            'nome'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:clientes,email',
            'telefone' => 'nullable|string|max:20',
            'cpf_cnpj' => 'nullable|string|max:20|unique:clientes,cpf_cnpj',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Execução com tratamento de exceções
        try {
            $cliente = Cliente::create($request->all());

            return response()->json([
                'message' => 'Cliente cadastrado com sucesso!',
                'data'    => $cliente
            ], 201);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Erro ao salvar no banco de dados. Verifique a integridade dos dados.'
            ], 400);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro interno ao processar o cadastro.'
            ], 500);
        }
    }

    /**
     * Update - Atualizar cliente existente
     */
    public function update(Request $request, $id)
    {
        abort_unless($request->user()->canEditRecords(), 403);

        // 1. Validação de formulário
        $validator = Validator::make($request->all(), [
            'nome'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('clientes', 'email')->ignore($id)],
            'telefone' => 'nullable|string|max:20',
            'cpf_cnpj' => ['nullable', 'string', 'max:20', Rule::unique('clientes', 'cpf_cnpj')->ignore($id)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Execução com tratamento de exceções
        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->update($request->all());

            return response()->json([
                'message' => 'Cliente atualizado com sucesso!'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cliente não encontrado para atualização.'
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

    /**
     * Destroy - Remover cliente
     */
    public function destroy($id)
    {
        abort_unless(request()->user()->canDeleteRecords(), 403);

        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();

            return response()->json([
                'message' => 'Cliente excluído com sucesso!'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cliente não encontrado para exclusão.'
            ], 404);

        } catch (QueryException $e) {
            // Trata o erro de Foreign Key (ex: cliente vinculado a vendas)
            return response()->json([
                'message' => 'Não é possível excluir este cliente pois ele possui registros vinculados.'
            ], 400);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro ao tentar excluir o cliente.'
            ], 500);
        }
    }
}