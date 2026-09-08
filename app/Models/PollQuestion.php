<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollQuestion extends Model
{
    protected $fillable = ['poll_id', 'question', 'type', 'options', 'order_number'];

    protected $casts = ['options' => 'array'];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function responses()
    {
        return $this->hasMany(PollResponse::class);
    }
}