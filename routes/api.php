<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BloodAlertController;
use App\Http\Controllers\PocheController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/

// Inscription des donneurs (Home.jsx)
Route::post('/donors', [DonorController::class, 'store']); 

// Connexion de l'agent (LoginModal.jsx)
Route::post('/login', [AuthController::class, 'login']); 

// Récupération de l'alerte actuelle (Home.jsx)
Route::get('/current-alert', [BloodAlertController::class, 'getCurrent']);

 


/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES (Nécessitent un Token)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {

    // 1. Dashboard de l'Agent (C'est cette ligne qui remplit ton registre)
    Route::get('/agent/dashboard', [PocheController::class, 'getDashboardData']);
    
    // 2. Enregistrement d'une nouvelle poche
    Route::post('/poches', [PocheController::class, 'store']);

    // Route pour enregistrer la sortie d'une poche spécifique
    Route::post('/poches/{id}/sortie', [PocheController::class, 'handleSortie']);

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Liste des donneurs (AdminDash.jsx)
    Route::get('/donors', [DonorController::class, 'index']);
    
    // Publication d'une alerte
    Route::post('/admin/publish-alert', [BloodAlertController::class, 'publish']);

    // Infos de l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Vérifie que tu as bien ajouté cette ligne
    Route::get('/admin/poches', [AdminController::class, 'getAllPoches']);

    // Supprime les deux anciennes lignes et remplace par celle-ci
Route::patch('/donors/{id}/status', [DonorController::class, 'updateStatus']);

// Et assure-toi que la route des stats est bien là :
Route::get('/admin/stats-globales', [AdminController::class, 'getStatsGlobales']);
    
});


