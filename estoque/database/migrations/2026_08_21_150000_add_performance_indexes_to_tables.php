<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->index(['ativo', 'quantidade_estoque'], 'produtos_ativo_quantidade_estoque_index');
            $table->index('nome', 'produtos_nome_index');
        });

        Schema::table('movimentacoes', function (Blueprint $table) {
            $table->index(['produto_id', 'created_at'], 'movimentacoes_produto_id_created_at_index');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->index('nome', 'clientes_nome_index');
        });

        Schema::table('fornecedores', function (Blueprint $table) {
            $table->index('razao_social', 'fornecedores_razao_social_index');
            $table->index('nome_fantasia', 'fornecedores_nome_fantasia_index');
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropIndex('produtos_ativo_quantidade_estoque_index');
            $table->dropIndex('produtos_nome_index');
        });

        Schema::table('movimentacoes', function (Blueprint $table) {
            $table->dropIndex('movimentacoes_produto_id_created_at_index');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex('clientes_nome_index');
        });

        Schema::table('fornecedores', function (Blueprint $table) {
            $table->dropIndex('fornecedores_razao_social_index');
            $table->dropIndex('fornecedores_nome_fantasia_index');
        });
    }
};
