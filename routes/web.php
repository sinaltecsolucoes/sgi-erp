<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EntidadeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // 1. Rotas de PERFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. Rotas de Cadastros
    //2.1 Usuários
    Route::resource('usuarios', UsuarioController::class);
    //2.2 Entidades
    Route::resource('entidades', EntidadeController::class);
    // Rota de dados para o DataTables
    Route::get('entidades/data/{pageType}', [EntidadeController::class, 'dataTable'])->name('entidades.data');
    // Rotas de listagem para cada tipo (Cliente/Fornecedor/Transportadora)
    Route::get('clientes', [EntidadeController::class, 'index'])->name('clientes.index')->defaults('pageType', 'cliente');
    Route::get('fornecedores', [EntidadeController::class, 'index'])->name('fornecedores.index')->defaults('pageType', 'fornecedor');


    // Rota para buscar dados do CNPJ via AJAX (chamada da API)
    Route::get('/api/cnpj/{cnpj}', [EntidadeController::class, 'buscarCnpjApi'])->name('api.cnpj');
});

require __DIR__ . '/auth.php';
