<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

class FornecedorApiController extends Controller
{
    // GET /api/fornecedores
    public function index(Request $request)
    {
        // Se for requisição da tabela (DataTables/AJAX)
        if ($request->ajax()) {
            $data = Fornecedor::select(['id', 'razao_social', 'nome_fantasia', 'cnpj', 'email', 'telefone']);

            return DataTables::of($data)
                ->addColumn('acoes', function ($row) {
                    return '
                        <button type="button" class="btn btn-sm btn-outline-laravel py-0 px-2 btn-editar" data-id="' . $row->id . '" title="Editar Fornecedor">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-laravel py-0 px-2 btn-excluir" data-id="' . $row->id . '" title="Excluir Fornecedor">
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

        $validator = Validator::make($request->all(), [
            'razao_social'  => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj'          => [
                'required',
                'string',
                'max:20',
                'unique:fornecedores,cnpj',
                function ($attribute, $value, $fail) {
                    if (!$this->validarCNPJ($value)) {
                        $fail('O CNPJ informado é inválido.');
                    }
                },
            ],
            'email'         => 'nullable|email|max:255',
            'telefone'      => [
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fornecedor = Fornecedor::create($request->all());

            return response()->json([
                'message' => 'Fornecedor cadastrado com sucesso!',
                'data'    => $fornecedor
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

    // GET /api/fornecedores/{id}
    public function show($id)
    {
        try {
            $fornecedor = Fornecedor::findOrFail($id);
            return response()->json($fornecedor);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Fornecedor não encontrado para exibição/edição.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar os dados do fornecedor.'
            ], 500);
        }
    }

    // PUT /api/fornecedores/{id}
    public function update(Request $request, $id)
    {
        abort_unless($request->user()->canEditRecords(), 403);

        $validator = Validator::make($request->all(), [
            'razao_social'  => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj'          => [
                'required',
                'string',
                'max:20',
                Rule::unique('fornecedores', 'cnpj')->ignore($id),
                function ($attribute, $value, $fail) {
                    if (!$this->validarCNPJ($value)) {
                        $fail('O CNPJ informado é inválido.');
                    }
                },
            ],
            'email'         => 'nullable|email|max:255',
            'telefone'      => [
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fornecedor = Fornecedor::findOrFail($id);
            $fornecedor->update($request->all());

            return response()->json([
                'message' => 'Fornecedor atualizado com sucesso!'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Fornecedor não encontrado para atualização.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro interno ao processar a requisição.'
            ], 500);
        }
    }

    // DELETE /api/fornecedores/{id}
    public function destroy($id)
    {
        abort_unless(request()->user()->canDeleteRecords(), 403);

        try {
            $fornecedor = Fornecedor::findOrFail($id);
            $fornecedor->delete();

            return response()->json([
                'message' => 'Fornecedor excluído com sucesso!'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Fornecedor não encontrado para exclusão.'
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Não é possível excluir este fornecedor pois ele possui registros vinculados.'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro ao tentar excluir o fornecedor.'
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
     * Métodos Privados de Validação de CNPJ
     */
    private function validarCNPJ(?string $valor): bool
    {
        if (empty($valor)) return true;

        $cnpj = preg_replace('/[^\d]/', '', $valor);

        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1+$/', $cnpj)) {
            return false;
        }

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
