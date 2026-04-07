<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // On valide le matricule et le mot de passe
        $credentials = $request->validate([
            'matricule' => 'required',
            'password' => 'required',
        ]);

        // Tentative de connexion avec le matricule
        if (!Auth::attempt(['matricule' => $credentials['matricule'], 'password' => $credentials['password']])) {
            return response()->json([
                'message' => 'Matricule ou mot de passe incorrect.'
            ], 401);
        }

        $user = Auth::user();
        /** @var \App\Models\User $user */
        $token = $user->createToken('main')->plainTextToken;

        return response()->json([
            'user' => [
                'name' => $user->name,
                'matricule' => $user->matricule,
                'role' => $user->role, // 'agent' ou 'admin'
            ],
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté avec succès']);
    }
}