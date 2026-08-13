<?php

use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FornecedorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('produtos', ProdutoController::class);
Route::resource('clientes', ClienteController::class);
Route::resource('fornecedores', FornecedorController::class);
