<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavigationMenu extends Model
{
    use HasFactory;

    protected $table = 'navigation_menus';

    protected $fillable = [
        'title',
        'url',
        'icon',
        'order',
        'location',
        'is_active',
    ];
}