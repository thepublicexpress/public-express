<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollResponse extends Model
{
    protected $fillable = ['poll_id', 'question_id', 'seat_id', 'user_id', 'selected_option', 'ip_address', 'user_agent'];

    public static function alreadyVoted($pollId, $seatId, $questionId, $ip, $userId = null)
    {
        $query = self::where('poll_id', $pollId)
            ->where('seat_id', $seatId)
            ->where('question_id', $questionId);

        return $query->where(function($q) use ($ip, $userId) {
            $q->where('ip_address', $ip);
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->exists();
    }
}