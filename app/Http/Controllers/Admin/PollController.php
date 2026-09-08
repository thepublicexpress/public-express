<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollQuestion;
use App\Models\PollResponse;
use App\Models\PollResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::with('questions')->withCount('responses')->latest()->get();
        $activePoll = $polls->firstWhere('is_active', true);
        $totalVotes = PollResponse::count();
        $totalQuestions = PollQuestion::count();
        $activePolls = Poll::where('is_active', true)->count();
        $results = PollResult::with(['poll', 'question', 'seat'])
            ->select('poll_results.*')
            ->latest()
            ->get()
            ->groupBy('poll_id');

        return view('admin.poll-results', compact(
            'polls', 'activePoll', 'totalVotes', 'totalQuestions', 'activePolls', 'results'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validatedPoll($request);
        $poll = Poll::create($data);
        $this->syncQuestions($poll, $request->input('questions', []));

        return back()->with('success', 'पोल सफलतापूर्वक बनाया गया।');
    }

    public function update(Request $request, Poll $poll)
    {
        $poll->update($this->validatedPoll($request));
        $poll->questions()->delete();
        $this->syncQuestions($poll, $request->input('questions', []));

        return back()->with('success', 'पोल अपडेट कर दिया गया।');
    }

    public function toggle(Poll $poll)
    {
        DB::transaction(function () use ($poll) {
            if (!$poll->is_active) {
                Poll::where('id', '!=', $poll->id)->update(['is_active' => false]);
            }
            $poll->update(['is_active' => !$poll->is_active]);
        });

        return back()->with('success', $poll->is_active ? 'पोल सक्रिय कर दिया गया।' : 'पोल बंद कर दिया गया।');
    }

    public function destroy(Poll $poll)
    {
        $poll->delete();
        return back()->with('success', 'पोल हटा दिया गया।');
    }

    private function validatedPoll(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function syncQuestions(Poll $poll, array $questions): void
    {
        foreach ($questions as $order => $question) {
            if (blank($question['question'] ?? null) || blank($question['options'] ?? null)) {
                continue;
            }

            $options = collect(preg_split('/\r?\n/', $question['options']))
                ->map(fn ($option) => trim($option))
                ->filter()
                ->map(fn ($label) => ['label' => $label, 'value' => str($label)->slug('_')->toString()])
                ->values()
                ->all();

            if ($options) {
                $poll->questions()->create([
                    'question' => $question['question'],
                    'type' => 'single',
                    'options' => $options,
                    'order_number' => $order,
                ]);
            }
        }
    }
}