<?php

use App\Http\Controllers\Api\FornecedorApiController;
use Illuminate\Support\Facades\Route;

Route::get('/fornecedores/{fornecedor}/edit', [FornecedorApiController::class, 'edit'])->name('api.fornecedores.edit');

Route::apiResource('fornecedores', FornecedorApiController::class)->names('api.fornecedores');
