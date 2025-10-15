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
        // Tabela: tbl_usuario -> usuarios
        Schema::create('usuarios', function (Blueprint $table) {
            // Mapeando id_usuario (PK)
            $table->id('id_usuario');

            $table->string('usu_nome', 150);
            $table->string('usu_login', 100)->unique(); // O login deve ser único

            // Necessário para criptografia de senha no Laravel
            $table->string('usu_senha', 255);

            $table->string('usu_equipe', 30)->nullable();
            $table->string('usu_nivel', 30); // Nível de acesso/permissão

            // Opcional: Para o sistema de "lembrar-me" do Laravel (Boas práticas de segurança)
            $table->rememberToken();
            $table->timestamps(); // Adicionando created_at/updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
