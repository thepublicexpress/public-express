<?php
// app/Models/UserLocation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $table = 'user_locations';

    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'address',
        'city',
        'state',
        'pincode',
        'is_current',
        'location_at'
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'location_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}