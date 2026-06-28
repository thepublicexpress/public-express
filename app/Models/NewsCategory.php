<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'news_categories';

    protected $fillable = [
        'name',
        'name_hi',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'parent_id',
        'order',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // ===== ACCESSORS =====
    public function getDisplayNameAttribute()
    {
        return $this->name_hi ?? $this->name;
    }

    // ===== RELATIONSHIPS =====
    public function news()
    {
        return $this->hasMany(News::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(NewsCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(NewsCategory::class, 'parent_id');
    }

    // ===== SCOPES =====
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParentCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    // ===== MUTATORS =====
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $value)));
    }
}