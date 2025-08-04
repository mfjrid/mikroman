<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MikrotikController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('mikrotiks', MikrotikController::class);
Route::post('/mikrotiks/{mikrotik}/test-connection', [MikrotikController::class, 'testConnection'])->name('mikrotiks.test');

Route::get('/monitor/{mikrotik}', [DashboardController::class, 'monitor'])->name('monitor');
Route::get('/api/stats/{mikrotik}', [DashboardController::class, 'getStats'])->name('api.stats');
