<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Rotas que não exigem autenticação
Route::post('login', [AuthController::class, 'login']);
