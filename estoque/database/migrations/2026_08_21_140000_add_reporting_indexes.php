<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimentacoes', function (Blueprint $table) {
            $table->index(['created_at', 'tipo'], 'movimentacoes_created_at_tipo_index');
        });

        Schema::table('produtos', function (Blueprint $table) {
            $table->index(['ativo', 'categoria_id'], 'produtos_ativo_categoria_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('movimentacoes', function (Blueprint $table) {
            $table->dropIndex('movimentacoes_created_at_tipo_index');
        });

        Schema::table('produtos', function (Blueprint $table) {
            $table->dropIndex('produtos_ativo_categoria_id_index');
        });
    }
};
