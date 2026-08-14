<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration {
    public function up() {
        // Executa o DatabaseSeeder completo com todas as factories
        //Artisan::call('db:seed', ['--force' => true]);
    }

    public function down() {
        // Opcional
    }
};
