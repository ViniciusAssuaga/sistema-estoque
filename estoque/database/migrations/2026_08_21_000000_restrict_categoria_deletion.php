<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE produtos DROP CONSTRAINT IF EXISTS produtos_categoria_id_foreign');
        DB::statement('ALTER TABLE produtos ADD CONSTRAINT produtos_categoria_id_foreign FOREIGN KEY (categoria_id) REFERENCES categorias (id) ON DELETE RESTRICT');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE produtos DROP CONSTRAINT IF EXISTS produtos_categoria_id_foreign');
        DB::statement('ALTER TABLE produtos ADD CONSTRAINT produtos_categoria_id_foreign FOREIGN KEY (categoria_id) REFERENCES categorias (id) ON DELETE SET NULL');
    }
};
