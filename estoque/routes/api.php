<?php

use App\Http\Controllers\Api\FornecedorApiController;
use App\Http\Controllers\Api\CategoriaApiController;
use App\Http\Controllers\Api\MovimentacaoController;

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'throttle:api'])->group(function () {
	Route::apiResource('fornecedores', FornecedorApiController::class)->names('api.fornecedores');
	Route::apiResource('categorias', CategoriaApiController::class)->names('api.categorias');
	Route::apiResource('movimentacoes', MovimentacaoController::class)
		->only(['index', 'store'])
		->names('api.movimentacoes');
});
