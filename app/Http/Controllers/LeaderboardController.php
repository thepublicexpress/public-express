<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index()
    {
        $reporters = User::where('role_id', 4)
            ->where('is_active', true)
            ->orderByDesc('points')
            ->take(10)
            ->get();

        return view('leaderboard.index', compact('reporters'));
    }
}
