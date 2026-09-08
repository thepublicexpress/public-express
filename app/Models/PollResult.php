<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollResult extends Model
{
    protected $fillable = ['poll_id', 'seat_id', 'question_id', 'option_key', 'option_label', 'votes_count', 'percentage'];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function question()
    {
        return $this->belongsTo(PollQuestion::class);
    }

    public function seat()
    {
        return $this->belongsTo(AssemblySeat::class, 'seat_id');
    }
}