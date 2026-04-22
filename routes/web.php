<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';


Route::get('/setup-admin', function () {
    // On vérifie si l'admin existe déjà pour éviter les doublons
    $adminExists = User::where('matricule', 'ADMIN-001')->first();
    
    if ($adminExists) {
        return "L'admin avec le matricule ADMIN-001 existe déjà.";
    }

    User::create([
        'name'      => 'Administrateur SANG-VIE',
        'email'     => 'admin@sangvie.bj',
        'matricule' => 'ADMIN-001', // C'est ce que tu taperas dans "Matricule agent"
        'password'  => Hash::make('Soutenance2026'), // Ton mot de passe
        'role'      => 'admin', // Très important pour les droits d'accès
    ]);

    return "Compte Admin créé ! Matricule : ADMIN-001 | Pass : Soutenance2026";
});