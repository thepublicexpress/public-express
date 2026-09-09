<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollRespondent extends Model
{
    protected $fillable = ['poll_id', 'seat_id', 'user_id', 'respondent_name', 'respondent_mobile', 'ip_address', 'user_agent'];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function seat()
    {
        return $this->belongsTo(AssemblySeat::class, 'seat_id');
    }
}