<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'state_id',
        'name',
        'slug',
        'code',
        'status',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * ✅ Relationship with State
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * ✅ Relationship with News
     */
    public function news()
    {
        return $this->hasMany(News::class);
    }

    /**
     * ✅ Relationship with Tehsils
     */
    public function tehsils()
    {
        return $this->hasMany(Tehsil::class);
    }

    /**
     * ✅ Scope for active districts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}