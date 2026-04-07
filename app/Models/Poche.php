<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poche extends Model
{
    protected $fillable = [
    'code_barre',
    'type_produit',
    'date_prelevement',
    'date_peremption',
    'groupe',
    'status',
    'service_destinataire',
    'motif_sortie',
    'agent_id' 
];

    public function agent()
{
    return $this->belongsTo(User::class, 'agent_id');
}
}

