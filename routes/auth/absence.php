<?php

use App\Http\Controllers\AbsenceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Absence Auth Routes
|--------------------------------------------------------------------------
|
| Rotas protegidas por autenticação para Absence
| Todas essas rotas requerem token JWT válido
|
*/

Route::group(['prefix' => 'absence'], function () {
    // Listar todos os absences paginados
    Route::get('/', [AbsenceController::class, 'listar'])
        ->name('absence.listar');

    // Criar novo absence
    Route::post('/', [AbsenceController::class, 'incluir'])
        ->name('absence.incluir');

    // Buscar absence específico
    Route::get('/{id}', [AbsenceController::class, 'obterPorCodigo'])
        ->where('id', '[0-9]+')
        ->name('absence.obterPorCodigo');

    // Atualizar absence
    Route::put('/{id}', [AbsenceController::class, 'atualizar'])
        ->where('id', '[0-9]+')
        ->name('absence.atualizar');

    // Atualização parcial do absence
    Route::patch('/{id}', [AbsenceController::class, 'atualizar'])
        ->where('id', '[0-9]+')
        ->name('absence.atualizar_parcial');

    // Excluir absence
    Route::delete('/{id}', [AbsenceController::class, 'excluir'])
        ->where('id', '[0-9]+')
        ->name('absence.excluir');

    // Rotas adicionais específicas do domínio
    // Route::patch('/{id}/activate', [AbsenceController::class, 'activate'])
    //     ->where('id', '[0-9]+')
    //     ->name('absence.activate');
    
    // Route::patch('/{id}/deactivate', [AbsenceController::class, 'deactivate'])
    //     ->where('id', '[0-9]+')
    //     ->name('absence.deactivate');
});