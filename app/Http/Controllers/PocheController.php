<?php

namespace App\Http\Controllers;

use App\Models\Poche;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PocheController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // GET /api/poches
    // Liste toutes les poches — filtre optionnel par status
    // Ex: /api/poches?status=Disponible
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Poche::with('agent:id,name')
                      ->orderBy('date_peremption', 'asc'); // FIFO : les + proches de péremption en premier

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->get());
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/poches
    // Enregistrer une nouvelle poche
    // ─────────────────────────────────────────────────────────────
   public function store(Request $request)
{
    // Normalise le tiret long Unicode (−) en tiret ASCII (-) avant validation
    $request->merge([
        'groupe'       => str_replace('−', '-', $request->groupe ?? ''),
        'type_produit' => strtoupper(explode(' ', $request->type_produit ?? '')[0]),
    ]);

    $validated = $request->validate([
        'code_barre'       => 'required|string|unique:poches,code_barre',
        'type_produit'     => 'required|string|in:CGR,PFC,CPA',
        'date_prelevement' => 'required|date',
        'date_peremption'  => 'required|date|after:date_prelevement',
        'groupe'           => 'required|string',
    ]);

    $poche = Poche::create([
        'code_barre'       => $validated['code_barre'],
        'type_produit'     => $validated['type_produit'],
        'date_prelevement' => $validated['date_prelevement'],
        'date_peremption'  => $validated['date_peremption'],
        'groupe'           => $validated['groupe'],
        'status'           => 'Disponible',
        'agent_id'         => auth()->id() ?? null,
    ]);

    return response()->json([
        'message' => 'Poche enregistrée avec succès.',
        'poche'   => $poche,
    ], 201);
}
    // ─────────────────────────────────────────────────────────────
    // PATCH /api/poches/{id}
    // Mettre à jour le statut / les infos d'une poche
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $poche = Poche::findOrFail($id);

        $validated = $request->validate([
            'status'               => 'sometimes|string',
            'service_destinataire' => 'sometimes|nullable|string',
            'motif_sortie'         => 'sometimes|nullable|string',
        ]);

        $poche->update($validated);

        return response()->json([
            'message' => 'Poche mise à jour.',
            'poche'   => $poche,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/poches/{id}/sortie
    // Valider la sortie d'une poche vers un service
    // ─────────────────────────────────────────────────────────────
    public function handleSortie(Request $request, $id)
    {
        $poche = Poche::findOrFail($id);

        $request->validate([
            'service_destinataire' => 'required|string',
            'motif_sortie'         => 'required|string',
        ]);

        $poche->update([
            'status'               => 'Sorti',
            'service_destinataire' => $request->service_destinataire,
            'motif_sortie'         => $request->motif_sortie,
            'agent_id'             => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Sortie de poche validée avec succès.',
            'poche'   => $poche,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/dashboard
    // Données complètes pour le tableau de bord agent
    // ─────────────────────────────────────────────────────────────
    public function getDashboardData()
    {
        $now = Carbon::now();

        // ── Toutes les poches disponibles ──
        $pochesDisponibles = Poche::where('status', 'Disponible')->get();

        // ── Sorties du jour ──
        $sortiesDuJour = Poche::where('status', 'Sorti')
            ->with('agent:id,name')
            ->whereDate('updated_at', $now->toDateString())
            ->orderBy('updated_at', 'desc')
            ->get();

        // ── Stats KPI ──
        $stats = [
            'stock_total'      => Poche::where('status', 'Disponible')->count(),
            'stock_evolution'  => '+5% depuis hier',                              // statique pour l'instant
            'urgences_actives' => 0,                                              // à brancher quand urgences_demandes sera prête
            'peremption_24h'   => Poche::where('status', 'Disponible')
                                        ->where('date_peremption', '<=', $now->copy()->addDay()->toDateString())
                                        ->count(),
            'qualifiees_jour'  => Poche::where('status', 'Disponible')
                                        ->whereDate('created_at', $now->toDateString())
                                        ->count(),
        ];

        // ── Heatmap par groupe sanguin ──
        $groupes = ['O-','O+','A-','A+','B-','B+','AB-','AB+'];

        $heatmap = collect($groupes)->map(function ($groupe) use ($pochesDisponibles) {
            $count = $pochesDisponibles->where('groupe', $groupe)->count();

            if ($count === 0 || $count <= 4) {
                $status = 'crit';
            } elseif ($count <= 15) {
                $status = 'warn';
            } else {
                $status = 'ok';
            }

            // Formater le groupe pour l'affichage (O- → O−)
            $groupeDisplay = str_replace('-', '−', $groupe);

            return [
                'group'  => $groupeDisplay,
                'status' => $status,
                'count'  => $count,
            ];
        });

        // ── Timeline d'activité récente ──
        $timeline = Poche::latest('updated_at')
            ->take(6)
            ->get()
            ->map(function ($poche) {
                $isSortie = $poche->status === 'Sorti';
                $groupeDisplay = str_replace('-', '−', $poche->groupe);

                return [
                    'icon'  => $isSortie ? 'fa-truck-fast'  : 'fa-square-plus',
                    'color' => $isSortie ? '#60a5fa'         : '#10b981',
                    'bg'    => $isSortie ? 'rgba(59,130,246,.12)' : 'rgba(16,185,129,.12)',
                    'title' => ($isSortie ? 'Sortie ' : 'Réception ') . $poche->code_barre . ' · ' . $groupeDisplay,
                    'sub'   => $isSortie
                                ? 'Livré à : ' . ($poche->service_destinataire ?? 'Non précisé')
                                : 'Poche enregistrée au stock',
                    'time'  => $poche->updated_at->format('H:i'),
                ];
            });

        // ── Consommation par service ──
        $consommation = Poche::where('status', 'Sorti')
            ->whereNotNull('service_destinataire')
            ->get()
            ->groupBy('service_destinataire')
            ->map(fn($group) => $group->count())
            ->sortDesc();

        $totalSorties = $consommation->sum() ?: 1;
        $colors = ['#9d0208', '#f59e0b', '#3b82f6', 'rgba(255,255,255,.15)'];

        $consoFormatted = $consommation->values()->map(function ($count, $index) use ($consommation, $totalSorties, $colors) {
            $label = $consommation->keys()[$index];
            return [
                'label' => $label,
                'val'   => $count,
                'pct'   => (int) round(($count / $totalSorties) * 100),
                'color' => $colors[$index % count($colors)],
            ];
        })->values();

        return response()->json([
            'stats'    => $stats,
            'heatmap'  => $heatmap,
            'timeline' => $timeline,
            'conso'    => $consoFormatted,
        ]);
    }
}