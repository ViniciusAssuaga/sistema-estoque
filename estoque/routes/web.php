<?php

use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('produtos', ProdutoController::class);

?>
