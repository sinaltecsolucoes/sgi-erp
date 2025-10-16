<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;

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
    //2.1 Clientes
    Route::resource('clientes', ClienteController::class);
    
    // Rota para buscar dados do CNPJ via AJAX (chamada da API)
    Route::get('/api/cnpj/{cnpj}', [ClienteController::class, 'buscarCnpjApi']);
    Route::get('/api/cnpj/{cnpj}', [ClienteController::class, 'buscarCnpjApi'])->name('api.cnpj');
});

require __DIR__.'/auth.php';
