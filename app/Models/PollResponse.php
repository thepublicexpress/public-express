<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollResponse extends Model
{
    protected $fillable = ['poll_id', 'question_id', 'seat_id', 'user_id', 'respondent_name', 'respondent_mobile', 'selected_option', 'ip_address', 'user_agent'];

    public static function alreadyVoted($pollId, $seatId, $questionId, $ip, $mobile, $userId = null)
    {
        return self::where('poll_id', $pollId)
            ->where(function ($query) use ($ip, $mobile, $userId) {
                $query->where('ip_address', $ip)
                    ->orWhere('respondent_mobile', $mobile);

                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->where(function ($query) use ($seatId, $questionId) {
                $query->where('seat_id', $seatId)
                    ->where('question_id', $questionId);
            })
            ->exists();
    }

    public static function alreadyVotedInPoll($pollId, $ip, $mobile, $userId = null)
    {
        return self::where('poll_id', $pollId)
            ->where(function ($query) use ($ip, $mobile, $userId) {
                $query->where('ip_address', $ip)
                    ->orWhere('respondent_mobile', $mobile);

                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->exists();
    }
}