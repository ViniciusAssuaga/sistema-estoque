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
                ->addColumn('acoes', function ($row) {
                    return '
                        <button type="button" class="btn btn-sm btn-outline-laravel py-0 px-2 btn-editar" data-id="' . $row->id . '" title="Editar Cliente">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-laravel py-0 px-2 btn-excluir" data-id="' . $row->id . '" title="Excluir Cliente">
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

        $validator = Validator::make($request->all(), [
            'nome'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:clientes,email',
            'telefone' => [
                'nullable',
                'string',
                'max:30',
                function ($attribute, $value, $fail) use ($request) {
                    $ddi = $request->input('ddi');
                    if (!$this->validarTelefoneDdi($value, $ddi)) {
                        $fail('O número de telefone informado não é válido para o DDI selecionado.');
                    }
                },
            ],
            'cpf_cnpj' => [
                'nullable',
                'string',
                'max:20',
                'unique:clientes,cpf_cnpj',
                function ($attribute, $value, $fail) {
                    if (!$this->validarCpfCnpj($value)) {
                        $fail('O CPF/CNPJ informado é inválido.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

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

        $validator = Validator::make($request->all(), [
            'nome'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('clientes', 'email')->ignore($id)],
            'telefone' => [
                'nullable',
                'string',
                'max:30',
                function ($attribute, $value, $fail) use ($request) {
                    $ddi = $request->input('ddi');
                    if (!$this->validarTelefoneDdi($value, $ddi)) {
                        $fail('O número de telefone informado não é válido para o DDI selecionado.');
                    }
                },
            ],
            'cpf_cnpj' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('clientes', 'cpf_cnpj')->ignore($id),
                function ($attribute, $value, $fail) {
                    if (!$this->validarCpfCnpj($value)) {
                        $fail('O CPF/CNPJ informado é inválido.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

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
            return response()->json([
                'message' => 'Não é possível excluir este cliente pois ele possui registros vinculados.'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro ao tentar excluir o cliente.'
            ], 500);
        }
    }

    /**
     * Métodos Privados de Validação de Telefone por DDI
     */
    private function validarTelefoneDdi(?string $valor, ?string $ddiInput = null): bool
    {
        if (empty($valor)) return true;

        $valorLimpo = trim($valor);
        $ddi = '';

        if (!empty($ddiInput)) {
            if (preg_match('/^\+\d+/', trim($ddiInput), $matches)) {
                $ddi = $matches[0];
            }
        } else {
            $partes = explode(' ', $valorLimpo);
            if (str_starts_with($partes[0], '+')) {
                if (preg_match('/^\+\d+/', $partes[0], $matches)) {
                    $ddi = $matches[0];
                }
            }
        }

        // Se o valor contiver o DDI (ex: "+1 (202) 555-0123" ou "+1 202 555-0123"),
        // removemos a parte do DDI antes de contar os dígitos.
        if (!empty($ddi) && str_starts_with($valorLimpo, $ddi)) {
            $valorSemDdi = trim(substr($valorLimpo, strlen($ddi)));
            $numeroApenasDigitos = preg_replace('/[^\d]/', '', $valorSemDdi);
        } else {
            $numeroApenasDigitos = preg_replace('/[^\d]/', '', $valorLimpo);
        }

        $qtdDigitos = strlen($numeroApenasDigitos);

        $regrasDdi = [
            '+55'  => [10, 11],
            '+1'   => [10],
            '+351' => [9],
            '+34'  => [9],
            '+54'  => [10, 11],
        ];

        if (!empty($ddi) && array_key_exists($ddi, $regrasDdi)) {
            return in_array($qtdDigitos, $regrasDdi[$ddi]);
        }

        return $qtdDigitos >= 8 && $qtdDigitos <= 15;
    }

    /**
     * Métodos Privados de Validação de CPF/CNPJ
     */
    private function validarCpfCnpj(?string $valor): bool
    {
        if (empty($valor)) return true;

        $c = preg_replace('/[^\d]/', '', $valor);

        if (strlen($c) === 11) {
            return $this->validarCPF($c);
        } elseif (strlen($c) === 14) {
            return $this->validarCNPJ($c);
        }

        return false;
    }

    private function validarCPF(string $cpf): bool
    {
        if (preg_match('/^(\d)\1+$/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }

        return true;
    }

    private function validarCNPJ(string $cnpj): bool
    {
        if (preg_match('/^(\d)\1+$/', $cnpj)) return false;

        $tamanho = strlen($cnpj) - 2;
        $numeros = substr($cnpj, 0, $tamanho);
        $digitos = substr($cnpj, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;

        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }

        $resultado = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
        if ($resultado != $digitos[0]) return false;

        $tamanho++;
        $numeros = substr($cnpj, 0, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;

        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }

        $resultado = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
        return $resultado == $digitos[1];
    }
}
