<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    

    // TRÈS IMPORTANT : Dis à Laravel quels champs il peut remplir
    protected $fillable = [
        'fullname',
        'blood_group',
        'phone',
        'city',
        'status',
        'last_donation_date'
    ];
}