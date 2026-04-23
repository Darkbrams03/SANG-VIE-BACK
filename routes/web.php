<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';


Route::get('/setup-admin', function () {
    // Nettoyage pour repartir sur de bonnes bases
    User::whereIn('matricule', ['ADM-2026-999', 'MS-2026-001'])->delete();

    // 1. Création de l'Administrateur National
    User::create([
        'name' => 'Administrateur National',
        'email' => 'admin@sangvie.bj',
        'matricule' => 'ADM-2026-999',
        'password' => Hash::make('admin123'),
        'role' => 'admin'
    ]);

    // 2. Création de l'Agent de Santé (Dr. Diallo)
    User::create([
        'name' => 'Dr. Diallo',
        'email' => 'diallo@sangvie.bj',
        'matricule' => 'MS-2026-001',
        'password' => Hash::make('password'),
        'role' => 'agent'
    ]);

    return "Succès : L'Admin (ADM-2026-999) et l'Agent (MS-2026-001) sont prêts sur Railway !";
});