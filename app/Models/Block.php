<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $table = 'blocks';
    
    protected $fillable = [
        'tehsil_id',
        'name',
        'name_hi',
        'slug',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===== ACCESSORS =====
    public function getDisplayNameAttribute()
    {
        return $this->name_hi ?? $this->name;
    }

    // ===== RELATIONSHIPS =====
    public function tehsil()
    {
        return $this->belongsTo(Tehsil::class);
    }

    public function district()
    {
        return $this->hasOneThrough(District::class, Tehsil::class, 'id', 'id', 'tehsil_id', 'district_id');
    }

    // ===== SCOPES =====
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}