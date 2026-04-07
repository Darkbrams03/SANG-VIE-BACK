<?php

namespace App\Http\Controllers;

use App\Models\Poche;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PocheController extends Controller
{
    /**
     
     */
    public function getDashboardData()
{
   
    $poches = Poche::orderBy('created_at', 'desc')->get();

   
    $sorties = Poche::where('status', 'Sorti')
                    ->with('agent:id,name')
                    ->orderBy('updated_at', 'desc') 
                    ->get();

   
    $stats = [
        'total'    => Poche::count(),
        'analysis' => Poche::where('status', 'En Analyse')->count(),
        'outputs'  => Poche::where('status', 'Sorti')->count(), // Compte réel des sorties
        'alerts'   => Poche::where('date_peremption', '<=', now()->addDays(7))
                          ->where('status', '!=', 'Sorti')
                          ->count(),
    ];

    
   $recentActivity = Poche::latest('updated_at')->take(5)->get()->map(function($poche) {
    return [
        'id'     => $poche->code_barre, // On envoie le code barre
        'type'   => $poche->status === 'Sorti' ? 'Sortie' : 'Entrée Stock',
        'groupe' => $poche->groupe,
        'heure'  => $poche->updated_at->diffForHumans(), // Correspond à response.data.recent_activity[].heure
    ];
});

    return response()->json([
        'stocks'          => $poches,
        'sorties'         => $sorties, // On envoie enfin les données à React
        'stats'           => $stats,
        'recent_activity' => $recentActivity
    ]);
}

    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'code_barre'       => 'required|unique:poches',
            'type_produit'     => 'required',
            'date_prelevement' => 'required|date',
            'date_peremption'  => 'required|date',
            'groupe'           => 'required',
        ]);

        // Création avec statut par défaut
        $poche = Poche::create([
            'code_barre'       => $validated['code_barre'],
            'type_produit'     => $validated['type_produit'],
            'date_prelevement' => $validated['date_prelevement'],
            'date_peremption'  => $validated['date_peremption'],
            'groupe'           => $validated['groupe'],
            'status'           => 'Disponible' 
        ]);

        return response()->json([
            'message' => 'Poche enregistrée avec succès',
            'poche'   => $poche
        ], 201);
    }

    public function handleSortie(Request $request, $id)
{
    $poche = Poche::findOrFail($id);

    // On met à jour les infos de sortie
    $poche->update([
        'status' => 'Sorti',
        'motif_sortie' => $request->motif,
        'service_destinataire' => $request->service,
        'agent_id'             => auth()->id(),
        'date_sortie' => now(), 
    ]);

    return response()->json([
        'message' => 'Sortie de poche validée avec succès',
        'poche' => $poche
    ]);
}
}