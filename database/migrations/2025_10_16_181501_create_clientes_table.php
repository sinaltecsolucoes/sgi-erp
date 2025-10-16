<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            // CHAVE ESTRANGEIRA: Liga este cliente à sua identidade na tabela 'entidades'
            $table->foreignId('entidade_id')->constrained('entidades')->onDelete('cascade');

            // Campos específicos do MÓDULO CLIENTE / FINANCEIRO
            $table->decimal('limite_credito', 10, 2)->default(0.00);
            $table->boolean('is_transportadora')->default(false); // Pode ser cliente e transportadora

            $table->timestamps();

            // Garante que uma entidade só pode ser cliente uma vez
            $table->unique('entidade_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
