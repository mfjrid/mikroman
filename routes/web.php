<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MikrotikPPPController;
use App\Http\Controllers\MikrotikUserController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('mikrotiks', MikrotikController::class);
Route::post('/mikrotiks/{mikrotik}/test-connection', [MikrotikController::class, 'testConnection'])->name('mikrotiks.test');

Route::get('/monitor/{mikrotik}', [DashboardController::class, 'monitor'])->name('monitor');
Route::get('/api/stats/{mikrotik}', [DashboardController::class, 'getStats'])->name('api.stats');

// User Management Routes
Route::prefix('mikrotik/{mikrotik}/users')->group(function () {
    Route::get('/', [MikrotikUserController::class, 'index'])->name('mikrotik-users.index');
    Route::get('/create', [MikrotikUserController::class, 'create'])->name('mikrotik-users.create');
    Route::post('/', [MikrotikUserController::class, 'store'])->name('mikrotik-users.store');
    Route::get('/{userId}/edit', [MikrotikUserController::class, 'edit'])->name('mikrotik-users.edit');
    Route::put('/{userId}', [MikrotikUserController::class, 'update'])->name('mikrotik-users.update');
    Route::delete('/{userId}', [MikrotikUserController::class, 'destroy'])->name('mikrotik-users.destroy');
    Route::post('/{userId}/toggle-status', [MikrotikUserController::class, 'toggleStatus'])->name('mikrotik-users.toggle-status');
});

// PPP Secret Management Routes
Route::prefix('mikrotik/{mikrotik}/ppp-secrets')->group(function () {
    Route::get('/', [MikrotikPPPController::class, 'index'])->name('mikrotik-ppp.index');
    Route::get('/create', [MikrotikPPPController::class, 'create'])->name('mikrotik-ppp.create');
    Route::post('/', [MikrotikPPPController::class, 'store'])->name('mikrotik-ppp.store');
    Route::get('/{secretId}/edit', [MikrotikPPPController::class, 'edit'])->name('mikrotik-ppp.edit');
    Route::put('/{secretId}', [MikrotikPPPController::class, 'update'])->name('mikrotik-ppp.update');
    Route::delete('/{secretId}', [MikrotikPPPController::class, 'destroy'])->name('mikrotik-ppp.destroy');
    Route::post('/{activeId}/disconnect', [MikrotikPPPController::class, 'disconnect'])->name('mikrotik-ppp.disconnect');
    Route::get('/refresh-status', [MikrotikPPPController::class, 'refreshStatus'])->name('mikrotik-ppp.refresh');
});
