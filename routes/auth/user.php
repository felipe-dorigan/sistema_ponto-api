<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::group([
    'prefix' => 'users',
    'as' => 'users.',
], function () {
    Route::get('/', [UserController::class, 'listar'])->name('listar');
    Route::post('/', [UserController::class, 'incluir'])->name('incluir');
    Route::get('/{user}', [UserController::class, 'obterPorCodigo'])->name('obterPorCodigo');
    Route::put('/{user}', [UserController::class, 'atualizar'])->name('atualizar');
    Route::delete('/{user}', [UserController::class, 'excluir'])->name('excluir');
});