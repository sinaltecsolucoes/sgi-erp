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
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id('end_codigo'); // Usando a chave primaria original para referência
            
            // Relacionamento N:1 com Entidades
            $table->foreignId('end_entidade_id')->constrained('entidades', 'id')->onDelete('cascade');
            
            $table->string('end_tipo_endereco', 50)->comment('Ex: Principal, Comercial, Entrega, Cobrança');
            
            $table->string('end_cep', 8);
            $table->string('end_logradouro', 200);
            $table->string('end_numero', 20);
            $table->string('end_complemento', 100)->nullable();
            $table->string('end_bairro', 80);
            $table->string('end_cidade', 80);
            $table->char('end_uf', 2);
            
            // Campos de auditoria
            $table->unsignedBigInteger('end_usuario_cadastro_id'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};
