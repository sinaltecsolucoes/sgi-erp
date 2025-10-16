<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Usuario::create([
            'usu_nome' => 'Administrador',
            'usu_login' => 'admin',
            'usu_senha' => '12345678', // Senha será hasheada pelo Model
            'usu_nivel' => 'Admin',
            'usu_equipe' => 'TI',
        ]);
    }
}
