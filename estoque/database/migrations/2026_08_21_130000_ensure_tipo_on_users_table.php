<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS tipo SMALLINT NOT NULL DEFAULT 0");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS tipo');
    }
};
