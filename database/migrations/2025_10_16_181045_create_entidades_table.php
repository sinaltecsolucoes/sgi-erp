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
        // Tabela central de pessoas jurídicas/físicas (Clientes, Fornecedores, Transportadoras)
        Schema::create('entidades', function (Blueprint $table) {
            $table->id(); // Chave primária

            // 1. DADOS DE IDENTIFICAÇÃO (CNPJ/CPF)
            $table->char('tipo_pessoa', 1)->comment('J=Jurídica, F=Física');
            $table->string('cnpj_cpf', 14)->unique()->nullable();
            $table->string('insc_estadual', 40)->nullable();

            // 2. NOMES
            $table->string('nome_fantasia', 200)->nullable(); 
            $table->string('razao_social', 200)->nullable(); 

            // 3. ENDEREÇO (Preenchido via API/ViaCEP)
            $table->string('cep', 8)->nullable();
            $table->string('endereco', 200)->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 80)->nullable();
            $table->string('cidade', 80)->nullable();
            $table->char('uf', 2)->nullable();

            // 4. CONTATO
            $table->string('telefone', 15)->nullable();
            $table->string('email', 100)->unique()->nullable();

            // 5. STATUS
            $table->boolean('ativo')->default(true);

            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entidades');
    }
};
