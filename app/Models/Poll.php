<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = ['question', 'options', 'is_active'];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
    ];
}
