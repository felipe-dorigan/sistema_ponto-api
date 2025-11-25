<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rotas públicas
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

// Rotas protegidas (requer autenticação)
Route::middleware('auth:sanctum')->group(function () {
    // Autenticação
    Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);

    // Registros de ponto
    Route::get('/time-records', [App\Http\Controllers\Api\TimeRecordController::class, 'index']);
    Route::post('/time-records', [App\Http\Controllers\Api\TimeRecordController::class, 'store']);
    Route::get('/time-records/{id}', [App\Http\Controllers\Api\TimeRecordController::class, 'show']);
    Route::post('/time-records/quick-entry', [App\Http\Controllers\Api\TimeRecordController::class, 'quickEntry']);
    Route::get('/hour-bank', [App\Http\Controllers\Api\TimeRecordController::class, 'hourBank']);

    // Ausências
    Route::get('/absences', [App\Http\Controllers\Api\AbsenceController::class, 'index']);
    Route::post('/absences', [App\Http\Controllers\Api\AbsenceController::class, 'store']);
    Route::get('/absences/{id}', [App\Http\Controllers\Api\AbsenceController::class, 'show']);
    
    // Rotas admin
    Route::get('/admin/absences', [App\Http\Controllers\Api\AbsenceController::class, 'indexAll']);
    Route::patch('/admin/absences/{id}/status', [App\Http\Controllers\Api\AbsenceController::class, 'updateStatus']);
});
