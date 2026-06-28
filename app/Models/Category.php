<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'news_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'parent_id',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====
    
    public function news()
    {
        return $this->hasMany(News::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // ===== SCOPES =====
    
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeParentCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    // ===== ACCESSORS =====
    
    public function getNewsCountAttribute()
    {
        return $this->news()->where('status', 'published')->count();
    }

    public function getNewsCountPublishedAttribute()
    {
        return $this->news()->where('status', 'published')->count();
    }

    // ===== MUTATORS =====
    
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $value)));
    }
}