<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollQuestion;
use App\Models\PollResponse;
use App\Models\PollResult;
use App\Models\PollRespondent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    public function index(Request $request)
    {
        $polls = Poll::with('questions')->withCount('responses')->latest()->get();
        $activePoll = $polls->firstWhere('is_active', true);
        $filters = $request->only(['poll_id', 'district', 'seat_id', 'name', 'mobile', 'date_from', 'date_to']);
        $respondentQuery = PollRespondent::with(['poll', 'seat'])
            ->when($request->filled('poll_id'), fn ($query) => $query->where('poll_id', $request->poll_id))
            ->when($request->filled('district'), fn ($query) => $query->whereHas('seat', fn ($seat) => $seat->where('district', $request->district)))
            ->when($request->filled('seat_id'), fn ($query) => $query->where('seat_id', $request->seat_id))
            ->when($request->filled('name'), fn ($query) => $query->where('respondent_name', 'like', '%' . $request->name . '%'))
            ->when($request->filled('mobile'), fn ($query) => $query->where('respondent_mobile', 'like', '%' . $request->mobile . '%'))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date_to));

        $totalVotes = (clone $respondentQuery)->count();
        $totalQuestions = PollQuestion::count();
        $activePolls = Poll::where('is_active', true)->count();
        $results = PollResult::with(['poll', 'question', 'seat'])
            ->select('poll_results.*')
            ->when($request->filled('poll_id'), fn ($query) => $query->where('poll_id', $request->poll_id))
            ->when($request->filled('seat_id'), fn ($query) => $query->where('seat_id', $request->seat_id))
            ->latest()
            ->get()
            ->groupBy('poll_id');
        $seatSummaries = (clone $respondentQuery)
            ->select('poll_id', 'seat_id', DB::raw('COUNT(*) as respondent_count'))
            ->groupBy('poll_id', 'seat_id')
            ->orderByDesc('respondent_count')
            ->get();
        $respondents = $respondentQuery->latest()->paginate(25)->withQueryString();
        $districts = \App\Models\AssemblySeat::where('is_active', 1)->whereNotNull('district')->distinct()->orderBy('district')->pluck('district');
        $seats = \App\Models\AssemblySeat::where('is_active', 1)->orderBy('seat_number')->get();
        $chartData = [
            'labels' => $seatSummaries->map(fn ($summary) => ($summary->seat->seat_name ?? 'Unknown') . ' (' . ($summary->seat->district ?? '-') . ')')->values(),
            'values' => $seatSummaries->pluck('respondent_count')->values(),
        ];

        return view('admin.poll-results', compact(
            'polls', 'activePoll', 'totalVotes', 'totalQuestions', 'activePolls', 'results', 'seatSummaries',
            'respondents', 'districts', 'seats', 'filters', 'chartData'
        ));
    }

    public function export(Request $request)
    {
        $respondents = PollRespondent::with(['poll', 'seat'])
            ->when($request->filled('poll_id'), fn ($query) => $query->where('poll_id', $request->poll_id))
            ->when($request->filled('district'), fn ($query) => $query->whereHas('seat', fn ($seat) => $seat->where('district', $request->district)))
            ->when($request->filled('seat_id'), fn ($query) => $query->where('seat_id', $request->seat_id))
            ->when($request->filled('name'), fn ($query) => $query->where('respondent_name', 'like', '%' . $request->name . '%'))
            ->when($request->filled('mobile'), fn ($query) => $query->where('respondent_mobile', 'like', '%' . $request->mobile . '%'))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($respondents) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, ['Poll', 'District', 'Assembly', 'Name', 'Mobile', 'IP Address', 'Submitted At']);
            foreach ($respondents as $respondent) {
                fputcsv($output, [
                    $respondent->poll->title ?? '-',
                    $respondent->seat->district ?? '-',
                    $respondent->seat->seat_name ?? '-',
                    $respondent->respondent_name ?? '-',
                    $respondent->respondent_mobile ?? '-',
                    $respondent->ip_address,
                    optional($respondent->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($output);
        }, 'poll-report-' . now()->format('Y-m-d-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
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