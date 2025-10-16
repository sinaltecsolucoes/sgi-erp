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
        Schema::create('fornecedores', function (Blueprint $table) {
            $table->id();

            // CHAVE ESTRANGEIRA: Liga este fornecedor à sua identidade na tabela 'entidades'
            $table->foreignId('entidade_id')->constrained('entidades')->onDelete('cascade');

            // Campos específicos do MÓDULO FORNECEDOR / COMPRAS
            $table->string('ultima_compra_info', 255)->nullable();
            $table->boolean('is_materia_prima')->default(false);

            $table->timestamps();

            // Garante que uma entidade só pode ser fornecedora uma vez
            $table->unique('entidade_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fornecedors');
    }
};
