<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';


Route::get('/setup-admin', function () {
    // On nettoie TOUT pour être sûr
    App\Models\User::whereIn('matricule', ['ADMIN-001', 'ADM-2026-999', 'MS-2026-001'])->delete();

    // On crée l'Admin National (ton habitude locale)
    App\Models\User::create([
        'name' => 'Administrateur National',
        'email' => 'admin@sangvie.bj',
        'matricule' => 'ADM-2026-999',
        'password' => Illuminate\Support\Facades\Hash::make('admin123'),
        'role' => 'admin'
    ]);

    // On crée l'Agent (Dr. Diallo)
    App\Models\User::create([
        'name' => 'Dr. Diallo',
        'email' => 'diallo@sangvie.bj',
        'matricule' => 'MS-2026-001',
        'password' => Illuminate\Support\Facades\Hash::make('password'),
        'role' => 'agent'
    ]);

    return "Les comptes ADM-2026-999 et MS-2026-001 ont été créés avec succès !";
});