<?php

use App\Http\Controllers\ContaController;
use App\Http\Controllers\TransferenciaController;
use Illuminate\Support\Facades\Route;

Route::post('/transferencias', [TransferenciaController::class, 'store']);
Route::get('/contas/{id}', [ContaController::class, 'show']);
Route::get('/contas/{id}/movimentacoes', [ContaController::class, 'movimentacoes']);
