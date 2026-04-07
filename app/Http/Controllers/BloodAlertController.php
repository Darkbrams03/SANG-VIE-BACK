<?php

namespace App\Http\Controllers;

use App\Models\BloodAlert;
use Illuminate\Http\Request;

class BloodAlertController extends Controller
{
  public function publish(Request $request)
{
    // 1. On désactive toujours l'ancienne alerte active
    BloodAlert::where('is_active', true)->update(['is_active' => false]);

    // 2. Si la requête contient 'stop', on s'arrête là (l'alerte est supprimée)
    if ($request->has('stop') && $request->stop == true) {
        return response()->json(['message' => 'Alerte désactivée']);
    }

    // 3. Sinon, on crée la nouvelle alerte
    $alert = BloodAlert::create([
        'group' => $request->group,
        'needed_pockets' => $request->needed_pockets,
        'location' => $request->location,
        'is_active' => true
    ]);

    return response()->json($alert);
}

    // Pour la HomePage : Récupérer l'alerte en cours
    public function getCurrent()
    {
        $alert = BloodAlert::where('is_active', true)->first();
        return response()->json($alert);
    }

    
}