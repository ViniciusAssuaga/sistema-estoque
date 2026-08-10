<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-excluir" data-id="'.$row->id.'" title="Excluir Cliente">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['acoes'])
                ->make(true);
        }

        return view('clientes.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'telefone' => 'nullable|string|max:20',
            'cpf_cnpj' => 'nullable|string|max:20|unique:clientes,cpf_cnpj',
        ]);

        Cliente::create($request->all());

        return response()->json(['message' => 'Cliente cadastrado com sucesso!']);
    }

    public function edit(Cliente $cliente)
    {
        return response()->json($cliente);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id,
            'telefone' => 'nullable|string|max:20',
            'cpf_cnpj' => 'nullable|string|max:20|unique:clientes,cpf_cnpj,' . $cliente->id,
        ]);

        $cliente->update($request->all());

        return response()->json(['message' => 'Cliente atualizado com sucesso!']);
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json(['message' => 'Cliente excluído com sucesso!']);
    }
}
