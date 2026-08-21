<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'tipo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('tipo')->default(0)->after('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'tipo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('tipo');
            });
        }
    }
};
