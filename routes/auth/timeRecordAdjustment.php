<?php

use App\Http\Controllers\TimeRecordAdjustmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TimeRecordAdjustment Auth Routes
|--------------------------------------------------------------------------
|
| Rotas protegidas por autenticação para TimeRecordAdjustment
| Todas essas rotas requerem token JWT válido
|
*/

Route::group(['prefix' => 'time-record-adjustment'], function () {
    // Listar todos os time-record-adjustments paginados
    Route::get('/', [TimeRecordAdjustmentController::class, 'listar'])
        ->name('time-record-adjustment.listar');

    // Criar novo time-record-adjustment
    Route::post('/', [TimeRecordAdjustmentController::class, 'incluir'])
        ->name('time-record-adjustment.incluir');

    // Buscar time-record-adjustment específico
    Route::get('/{id}', [TimeRecordAdjustmentController::class, 'obterPorCodigo'])
        ->where('id', '[0-9]+')
        ->name('time-record-adjustment.obterPorCodigo');

    // Atualizar time-record-adjustment
    Route::put('/{id}', [TimeRecordAdjustmentController::class, 'atualizar'])
        ->where('id', '[0-9]+')
        ->name('time-record-adjustment.atualizar');

    // Atualização parcial do time-record-adjustment
    Route::patch('/{id}', [TimeRecordAdjustmentController::class, 'atualizar'])
        ->where('id', '[0-9]+')
        ->name('time-record-adjustment.atualizar_parcial');

    // Excluir time-record-adjustment
    Route::delete('/{id}', [TimeRecordAdjustmentController::class, 'excluir'])
        ->where('id', '[0-9]+')
        ->name('time-record-adjustment.excluir');

    // Aprovar solicitação de ajuste (apenas admin)
    Route::patch('/{id}/approve', [TimeRecordAdjustmentController::class, 'aprovar'])
        ->where('id', '[0-9]+')
        ->name('time-record-adjustment.aprovar');
    
    // Rejeitar solicitação de ajuste (apenas admin)
    Route::patch('/{id}/reject', [TimeRecordAdjustmentController::class, 'rejeitar'])
        ->where('id', '[0-9]+')
        ->name('time-record-adjustment.rejeitar');
});