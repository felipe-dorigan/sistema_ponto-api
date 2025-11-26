<?php

use App\Http\Controllers\TimeRecordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TimeRecord Auth Routes
|--------------------------------------------------------------------------
|
| Rotas protegidas por autenticação para TimeRecord
| Todas essas rotas requerem token JWT válido
|
*/

Route::group(['prefix' => 'time-record'], function () {
    // Listar todos os time-records paginados
    Route::get('/', [TimeRecordController::class, 'listar'])
        ->name('time-record.listar');

    // Criar novo time-record
    Route::post('/', [TimeRecordController::class, 'incluir'])
        ->name('time-record.incluir');

    // Buscar time-record específico
    Route::get('/{id}', [TimeRecordController::class, 'obterPorCodigo'])
        ->where('id', '[0-9]+')
        ->name('time-record.obterPorCodigo');

    // Atualizar time-record
    Route::put('/{id}', [TimeRecordController::class, 'atualizar'])
        ->where('id', '[0-9]+')
        ->name('time-record.atualizar');

    // Atualização parcial do time-record
    Route::patch('/{id}', [TimeRecordController::class, 'atualizar'])
        ->where('id', '[0-9]+')
        ->name('time-record.atualizar_parcial');

    // Excluir time-record
    Route::delete('/{id}', [TimeRecordController::class, 'excluir'])
        ->where('id', '[0-9]+')
        ->name('time-record.excluir');

    // Rotas adicionais específicas do domínio
    // Route::patch('/{id}/activate', [TimeRecordController::class, 'activate'])
    //     ->where('id', '[0-9]+')
    //     ->name('time-record.activate');
    
    // Route::patch('/{id}/deactivate', [TimeRecordController::class, 'deactivate'])
    //     ->where('id', '[0-9]+')
    //     ->name('time-record.deactivate');
});