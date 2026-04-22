<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodAlert extends Model
{
    protected $table = 'blood_alerts';

    protected $fillable = [
        'group',
        'needed_pockets',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}