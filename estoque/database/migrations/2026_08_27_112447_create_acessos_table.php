<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acessos', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_agent')->nullable();
            $table->unsignedBigInteger('total_acessos')->default(1);
            $table->timestamp('ultimo_acesso');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acessos');
    }
};
