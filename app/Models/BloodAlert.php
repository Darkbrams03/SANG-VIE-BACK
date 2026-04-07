<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BloodAlert extends Model
{
    protected $fillable = ['group', 'needed_pockets', 'location', 'is_active'];
}
