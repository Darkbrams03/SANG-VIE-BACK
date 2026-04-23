<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// ÉTAPE A : On vide le cache sur la page d'accueil
Route::get('/', function () {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return "Système SANG-VIE : Cache vidé ! Allez maintenant sur /force-setup pour créer les comptes.";
});

// ÉTAPE B : On crée l'Admin ET l'Agent
Route::get('/force-setup', function () {
    // Nettoyage des anciens tests pour éviter les erreurs "Existe déjà"
    User::whereIn('matricule', ['ADMIN-001', 'ADM-2026-999', 'MS-2026-001'])->delete();

    // 1. Création de l'Administrateur National
    User::create([
        'name' => 'Administrateur National',
        'email' => 'admin@sangvie.bj',
        'matricule' => 'ADM-2026-999',
        'password' => Hash::make('admin123'),
        'role' => 'admin'
    ]);

    // 2. Création de l'Agent (Dr. Diallo)
    User::create([
        'name' => 'Dr. Diallo',
        'email' => 'diallo@sangvie.bj',
        'matricule' => 'MS-2026-001',
        'password' => Hash::make('password'),
        'role' => 'agent'
    ]);

    return "C'est prêt ! Admin (ADM-2026-999) et Agent (MS-2026-001) créés sur Railway.";
});