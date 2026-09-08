<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = [
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
     * ✅ Relationship with News
     */
    public function news()
    {
        return $this->hasMany(News::class);
    }

    /**
     * ✅ Relationship with Users (Reporters)
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * ✅ Relationship with Districts
     */
    public function districts()
    {
        return $this->hasMany(District::class);
    }

    /**
     * ✅ Scope for active states
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}