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
            'sku' => 'required|string|max:100|unique:produtos,sku',
            'nome' => 'required|string|max:255',
            'preco_custo' => 'required|numeric|min:0',
            'preco_venda' => 'required|numeric|min:0',
            'quantidade_estoque' => 'required|integer|min:0',
            'estoque_minimo' => 'nullable|integer|min:0',
            'descricao' => 'nullable|string',
        ];
    }

    private function converterPreco($valor)
    {
        if (is_null($valor) || is_numeric($valor)) {
            return $valor;
        }

        $valorLimpo = str_replace('.', '', $valor);
        return (float) str_replace(',', '.', $valorLimpo);
    }
}
