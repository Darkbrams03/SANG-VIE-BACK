<?php

namespace App\Http\Controllers;

use App\Models\BloodAlert;
use Illuminate\Http\Request;

class BloodAlertController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // GET /api/current-alert  (PUBLIC — pas de token)
    // Retourne l'alerte active pour la bannière de la home
    // ─────────────────────────────────────────────────────────────
    public function currentAlert()
    {
        $alert = BloodAlert::where('is_active', true)
                           ->latest()
                           ->first();

        if (!$alert) {
            return response()->json(null);
        }

        return response()->json([
            'id'             => $alert->id,
            'group'          => $alert->group,
            'needed_pockets' => $alert->needed_pockets,
            'location'       => $alert->location,
            'is_active'      => $alert->is_active,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/blood-alerts  (PROTÉGÉ)
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        return response()->json(
            BloodAlert::latest()->take(20)->get()
        );
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/blood-alerts  (PROTÉGÉ — agent)
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'group'          => 'required|string',
            'needed_pockets' => 'required|integer|min:1',
        ]);

        // Désactiver les anciennes alertes actives
        BloodAlert::where('is_active', true)->update(['is_active' => false]);

        $alert = BloodAlert::create([
            'group'          => $request->group,
            'needed_pockets' => $request->needed_pockets,
            'location'       => 'CNHU-HKM (COTONOU)',
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'message' => 'Alerte créée.',
            'alert'   => $alert,
        ], 201);
    }
}