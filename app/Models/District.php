<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';
    
    protected $fillable = [
        'state_id',
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
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function tehsils()
    {
        return $this->hasMany(Tehsil::class);
    }

    // ===== SCOPES =====
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}