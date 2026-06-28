<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tehsil extends Model
{
    use HasFactory;

    protected $table = 'tehsils';
    
    protected $fillable = [
        'district_id',
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
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    // ===== SCOPES =====
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}