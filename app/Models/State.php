<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = 'states';
    
    protected $fillable = [
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
    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function tehsils()
    {
        return $this->hasManyThrough(Tehsil::class, District::class);
    }

    // ===== SCOPES =====
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}