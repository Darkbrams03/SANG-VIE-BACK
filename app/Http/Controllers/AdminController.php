<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poche;
use App\Models\User;
use App\Models\BloodAlert; // Correction du nom du modèle
use Carbon\Carbon; // Import crucial pour les dates

class AdminController extends Controller
{
    public function getStatsGlobales()
    {
        $groupes = ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'];
        $stocksParGroupe = [];

        foreach ($groupes as $groupe) {
            $stocksParGroupe[$groupe] = Poche::where('groupe', $groupe)
                ->where('status', 'Disponible')
                ->count();
        }

        return response()->json([
            'total_poches' => Poche::where('status', 'Disponible')->count(),
            
            // On compte les alertes de sang actuellement actives
            'urgences_actives' => BloodAlert::where('is_active', true)->count(), 
            
            // Poches périmées ou expirant dans moins de 2 jours
            'poches_perimees' => Poche::where('date_peremption', '<', Carbon::now()->addDays(2))
                ->where('status', 'Disponible')
                ->count(),
                
            'stocks_par_groupe' => $stocksParGroupe,
            
            'entrees_24h' => Poche::where('created_at', '>=', Carbon::now()->subDay())->count(),
            
            'sorties_24h' => Poche::where('status', 'Sorti')
                ->where('updated_at', '>=', Carbon::now()->subDay())
                ->count(),
        ]);
    }

    public function getAllPoches() 
    {
        return response()->json(Poche::orderBy('created_at', 'desc')->get());
    }
}