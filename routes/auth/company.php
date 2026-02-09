<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Company Auth Routes
|--------------------------------------------------------------------------
|
| Rotas protegidas por autenticação para Company
| Todas essas rotas requerem token JWT válido
|
*/

Route::group(['prefix' => 'company'], function () {
    // Listar todos os companys paginados
    Route::get('/', [CompanyController::class, 'listar'])
        ->name('company.listar');

    // Criar novo company
    Route::post('/', [CompanyController::class, 'incluir'])
        ->name('company.incluir');

    // Buscar company específico
    Route::get('/{id}', [CompanyController::class, 'obterPorCodigo'])
        ->where('id', '[0-9]+')
        ->name('company.obterPorCodigo');

    // Atualizar company
    Route::put('/{id}', [CompanyController::class, 'atualizar'])
        ->where('id', '[0-9]+')
        ->name('company.atualizar');

    // Atualização parcial do company
    Route::patch('/{id}', [CompanyController::class, 'atualizar'])
        ->where('id', '[0-9]+')
        ->name('company.atualizar_parcial');

    // Excluir company
    Route::delete('/{id}', [CompanyController::class, 'excluir'])
        ->where('id', '[0-9]+')
        ->name('company.excluir');

    // Rotas adicionais específicas do domínio
    // Route::patch('/{id}/activate', [CompanyController::class, 'activate'])
    //     ->where('id', '[0-9]+')
    //     ->name('company.activate');
    
    // Route::patch('/{id}/deactivate', [CompanyController::class, 'deactivate'])
    //     ->where('id', '[0-9]+')
    //     ->name('company.deactivate');
});