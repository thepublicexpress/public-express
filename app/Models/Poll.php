<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = ['title', 'description', 'start_date', 'end_date', 'is_active'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(PollQuestion::class);
    }

    public function responses()
    {
        return $this->hasMany(PollResponse::class);
    }

    public function results()
    {
        return $this->hasMany(PollResult::class);
    }
}