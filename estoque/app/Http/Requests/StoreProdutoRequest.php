<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sku' => $this->sku ? strtoupper($this->sku) : null,
            'preco_custo' => $this->converterPreco($this->preco_custo),
            'preco_venda' => $this->converterPreco($this->preco_venda),
        ]);
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:50|unique:produtos,sku',
            'nome' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'preco_custo' => 'required|numeric|min:0|max:99999999.99',
            'preco_venda' => 'required|numeric|min:0|max:99999999.99',
            'quantidade_estoque' => 'required|integer|min:0',
            'estoque_minimo' => 'nullable|integer|min:0',
            'descricao' => 'nullable|string',
            'ativo' => 'nullable|boolean',
        ];
    }

    private function converterPreco($valor)
    {
        if (is_null($valor) || is_numeric($valor)) {
            return $valor;
        }

        $valor = trim($valor);

        if (preg_match('/^\d{1,3}(\.\d{3})+,\d{1,2}$/', $valor)) {
            return (float) str_replace(',', '.', str_replace('.', '', $valor));
        }

        if (preg_match('/^\d+,\d{1,2}$/', $valor)) {
            return (float) str_replace(',', '.', $valor);
        }

        return $valor;
    }
}
