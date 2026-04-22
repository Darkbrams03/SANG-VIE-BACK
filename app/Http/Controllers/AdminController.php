<?php

namespace App\Http\Controllers;

use App\Models\Poche;
use App\Models\BloodAlert;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Donor;

class AdminController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // GET /api/admin/stats-globales
    // Stats pour la vue globale du dashboard admin
    // ─────────────────────────────────────────────────────────────
    public function statsGlobales()
    {
        $now = Carbon::now();

        // Stocks par groupe sanguin — clés courtes (O-, A+, etc.)
        $groupes = ['O-','O+','A-','A+','B-','B+','AB-','AB+'];
        $stocksParGroupe = [];
        foreach ($groupes as $g) {
            $stocksParGroupe[$g] = Poche::where('status', 'Disponible')
                                        ->where('groupe', $g)
                                        ->count();
        }

        return response()->json([
            'total_poches'     => Poche::where('status', 'Disponible')->count(),
            'urgences_actives' => BloodAlert::where('is_active', true)->count(),
            'poches_perimees'  => Poche::where('status', 'Disponible')
                                       ->where('date_peremption', '<=', $now->copy()->addDays(2)->toDateString())
                                       ->count(),
            'entrees_24h'      => Poche::whereDate('created_at', $now->toDateString())->count(),
            'sorties_24h'      => Poche::where('status', 'Sorti')
                                       ->whereDate('updated_at', $now->toDateString())
                                       ->count(),
            'stocks_par_groupe' => $stocksParGroupe,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/admin/poches
    // Inventaire complet pour l'admin (toutes les poches)
    // ─────────────────────────────────────────────────────────────
    public function getPoches(Request $request)
    {
        $query = Poche::with('agent:id,name')
                      ->orderBy('date_peremption', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->get());
    }

    // ─────────────────────────────────────────────────────────────
    // PATCH /api/admin/poches/{id}/destroy
    // Détruire une poche (admin uniquement)
    // ─────────────────────────────────────────────────────────────
    public function destroyPoche($id)
    {
        $poche = Poche::findOrFail($id);

        $poche->update([
            'status'               => 'Sorti',
            'service_destinataire' => 'ADMIN — DESTRUCTION',
            'motif_sortie'         => 'Péremption / Destruction — Décision admin',
            'agent_id'             => auth()->id(),
        ]);

        return response()->json(['message' => 'Poche détruite et retirée du stock.']);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/admin/publish-alert
    // Publier ou arrêter une alerte de mobilisation
    // ─────────────────────────────────────────────────────────────
    public function publishAlert(Request $request)
    {
        // Arrêter toutes les alertes actives
        if ($request->boolean('stop')) {
            BloodAlert::where('is_active', true)->update(['is_active' => false]);
            return response()->json(['message' => 'Alerte retirée.']);
        }

        $request->validate([
            'group'          => 'required|string',
            'needed_pockets' => 'required|integer|min:1',
            'location'       => 'sometimes|string',
        ]);

        // Désactiver les anciennes alertes
        BloodAlert::where('is_active', true)->update(['is_active' => false]);

        // Créer la nouvelle alerte active
        $alert = BloodAlert::create([
            'group'          => $request->group,
            'needed_pockets' => $request->needed_pockets,
            'location'       => $request->location ?? 'CNHU-HKM (COTONOU)',
            'is_active'      => true,
        ]);

        return response()->json([
            'message' => 'Alerte publiée avec succès.',
            'alert'   => $alert,
        ], 201);
    }
}