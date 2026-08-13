<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Regras de Validação Padrão
    |--------------------------------------------------------------------------
    */

    'required' => 'O campo :attribute é obrigatório.',
    'unique'   => 'Este :attribute já está cadastrado no sistema.',
    'numeric'  => 'O campo :attribute deve ser um número válido.',
    'integer'  => 'O campo :attribute deve ser um número inteiro.',
    'min'      => [
        'numeric' => 'O campo :attribute não pode ser menor que :min.',
    ],
    'max'      => [
        'string' => 'O campo :attribute não pode ter mais que :max caracteres.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Nomes Amigáveis dos Campos (Atributos)
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        // Produtos
        'sku'                => 'SKU / Código',
        'nome'               => 'Nome do Produto',
        'preco_custo'        => 'Preço de Custo',
        'preco_venda'        => 'Preço de Venda',
        'quantidade_estoque' => 'Quantidade em Estoque',
        'estoque_minimo'     => 'Estoque Mínimo',
        'descricao'          => 'Descrição',
        'ativo'              => 'Status Ativo',

        // Clientes
        'nome'              => 'Nome',
        'email'              => 'E-mail',
        'telefone'           => 'Telefone',
        'cpf_cnpj'              => 'CPF/CNPJ',
        'endereco'              => 'Endereço',

        // Fornecedores
        'razao_social'       => 'Razão Social',
        'nome_fantasia'      => 'Nome Fantasia',
        'cnpj'               => 'CNPJ',
        'email'              => 'E-mail',
        'telefone'           => 'Telefone',
    ],

];
