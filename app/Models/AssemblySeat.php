<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssemblySeat extends Model
{
    protected $fillable = ['seat_number', 'seat_name', 'district', 'constituency_type', 'current_mla', 'party', 'is_active'];
}