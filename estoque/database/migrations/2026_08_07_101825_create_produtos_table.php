<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as alterações no banco de dados.
     */
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id(); // Chave primária AUTO_INCREMENT
            
            // Dados de identificação do produto
            $table->string('sku', 50)->unique(); // Código único do produto (ex: PROD-001)
            $table->string('nome', 150);
            $table->text('descricao')->nullable(); // Descrição detalhada (opcional)
            
            // Valores e Estoque
            $table->decimal('preco_custo', 10, 2)->default(0.00);
            $table->decimal('preco_venda', 10, 2);
            $table->integer('quantidade_estoque')->default(0);
            $table->integer('estoque_minimo')->default(5);
            
            // Status e Lixeira
            $table->boolean('ativo')->default(true);
            $table->softDeletes(); // Cria a coluna deleted_at para exclusão lógica
            
            // Auditoria
            $table->timestamps(); // Cria created_at e updated_at
        });
    }

    /**
     * Reverte as alterações no banco de dados.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
