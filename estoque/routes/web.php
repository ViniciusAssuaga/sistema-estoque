<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redireciona a raiz para a tela de login
Route::get('/', function () {
    return redirect()->route('login');
});

// Todas as rotas do sistema protegidas por login
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard principal do sistema
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rotas do Perfil do Usuário (exigidas pela barra de navegação)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulos que utilizam API REST
    Route::get('/fornecedores', function () { return view('fornecedores.index'); })->name('fornecedores.index');
    Route::get('/categorias', function () { return view('categorias.index'); })->name('categorias.index');
    Route::get('/movimentacoes', function () { return view('movimentacoes.index'); })->name('movimentacoes.index');
    Route::get('produtos/listar-json', [ProdutoController::class, 'listarJson'])->name('produtos.listarJson');

    // Módulos com Resource Controllers
    Route::resource('produtos', ProdutoController::class);
    Route::resource('clientes', ClienteController::class);
    
});

require __DIR__.'/auth.php';
