<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PocheController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\BloodAlertController;
use App\Http\Controllers\AdminController;

// ─────────────────────────────────────────────────────────────
// ROUTES PUBLIQUES — pas de token requis
// ─────────────────────────────────────────────────────────────

Route::post('/login',        [AuthController::class, 'login']);
Route::post('/donors',       [DonorController::class, 'store']);
Route::get('/current-alert', [BloodAlertController::class, 'currentAlert']);
 Route::post('/publish-alert',        [AdminController::class, 'publishAlert']);
Route::get('/stats-globales',        [AdminController::class, 'statsGlobales']);
// ─────────────────────────────────────────────────────────────
// ROUTES PROTÉGÉES — token Bearer requis
// ─────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ── Auth ──
    Route::post('/logout', [AuthController::class, 'logout']);

    // ── Dashboard agent ──
    Route::get('/dashboard', [PocheController::class, 'getDashboardData']);

    // ── Poches ──
    Route::get('/poches',              [PocheController::class, 'index']);
    Route::post('/poches',             [PocheController::class, 'store']);
    Route::patch('/poches/{id}',       [PocheController::class, 'update']);
    Route::post('/poches/{id}/sortie', [PocheController::class, 'handleSortie']);

    // ── Alertes ──
    Route::get('/blood-alerts',  [BloodAlertController::class, 'index']);
    Route::post('/blood-alerts', [BloodAlertController::class, 'store']);

    // ── Donneurs (lecture admin/agent) ──
    Route::get('/donors',        [DonorController::class, 'index']);
    Route::patch('/donors/{id}', [DonorController::class, 'update']);

    // ── ADMIN uniquement ──
    Route::prefix('admin')->group(function () {
        
        Route::get('/poches',                [AdminController::class, 'getPoches']);
        Route::patch('/poches/{id}/destroy', [AdminController::class, 'destroyPoche']);
       
    });

});