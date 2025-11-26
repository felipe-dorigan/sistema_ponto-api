<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthController;

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

// Health Check Routes (sem autenticação)
Route::get('/health', [HealthController::class, 'check']);
Route::get('/ping', [HealthController::class, 'ping']);

Route::group([], function () {
    // Carrega dinamicamente todos os arquivos .php da pasta /guest
    foreach (glob(__DIR__ . '/guest/*.php') as $file) {
        require $file;
    }
});

Route::middleware('auth:api')->group(function () {
    // Carrega dinamicamente todos os arquivos .php da pasta /auth
    foreach (glob(__DIR__ . '/auth/*.php') as $file) {
        require $file;
    }
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Rota não encontrada.',
    ], 404);
});
