<?php

use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// Módulos que utilizam API REST (apenas carregam a view)
Route::get('/fornecedores', function () { return view('fornecedores.index'); })->name('fornecedores.index');
Route::get('/categorias', function () { return view('categorias.index'); })->name('categorias.index');
Route::get('/movimentacoes', function () { return view('movimentacoes.index'); })->name('movimentacoes.index');
Route::get('produtos/listar-json', [ProdutoController::class, 'listarJson'])->name('produtos.listarJson');

// Módulos que utilizam o sistema tradicional (Controllers com Blade)
Route::resource('produtos', ProdutoController::class);
Route::resource('clientes', ClienteController::class);
