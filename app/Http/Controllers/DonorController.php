<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'blood_group' => 'required|string',
            'phone' => 'required|string|unique:donors',
            'city' => 'required|string',
        ]);

        $donor = Donor::create($validated);

        return response()->json([
            'message' => 'Merci Héros ! Inscription réussie.',
            'donor' => $donor
        ], 201);
    }

    public function updateStatus(Request $request, Donor $donor)
    {
        $request->validate([
            'status' => 'required|in:pending,verified,deferred',
        ]);

        $donor->update(['status' => $request->status]);

        return response()->json(['message' => 'Statut mis à jour']);
    }

    public function index()
    {
        try {
            $donors = \App\Models\Donor::all(); 
            return response()->json($donors);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur Serveur',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} // <--- Ferme proprement ici, sans virgule.