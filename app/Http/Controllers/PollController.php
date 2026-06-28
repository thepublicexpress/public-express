<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    public function vote(Request $request)
    {
        $request->validate([
            'poll_id' => 'required|exists:polls,id',
            'option_index' => 'required|integer',
        ]);

        $ip = $request->ip();

        $exists = DB::table('poll_votes')
            ->where('poll_id', $request->poll_id)
            ->where('ip_address', $ip)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'आप पहले ही वोट दे चुके हैं।']);
        }

        DB::table('poll_votes')->insert([
            'poll_id' => $request->poll_id,
            'ip_address' => $ip,
            'option_index' => $request->option_index,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $results = $this->getResults($request->poll_id);

        return response()->json(['success' => true, 'results' => $results]);
    }

    private function getResults($poll_id)
    {
        $total = DB::table('poll_votes')->where('poll_id', $poll_id)->count();
        $votes = DB::table('poll_votes')
            ->select('option_index', DB::raw('count(*) as count'))
            ->where('poll_id', $poll_id)
            ->groupBy('option_index')
            ->get()
            ->pluck('count', 'option_index');

        return [
            'total' => $total,
            'votes' => $votes,
        ];
    }
}