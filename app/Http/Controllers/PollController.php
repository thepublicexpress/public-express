<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\AssemblySeat;
use App\Models\PollQuestion;
use App\Models\PollResponse;
use App\Models\PollResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PollController extends Controller
{
    // ✅ Get all seats
    public function getSeats()
    {
        $seats = AssemblySeat::where('is_active', 1)->orderBy('seat_number')->get();
        return response()->json(['success' => true, 'seats' => $seats]);
    }

    // Districts are stored as text on assembly_seats, so derive the list from active seats.
    public function getDistricts()
    {
        $districts = AssemblySeat::where('is_active', 1)
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->select('district')
            ->distinct()
            ->orderBy('district')
            ->pluck('district')
            ->values();

        return response()->json(['success' => true, 'districts' => $districts]);
    }

    public function getSeatsByDistrict($district)
    {
        $seats = AssemblySeat::where('is_active', 1)
            ->where('district', $district)
            ->orderBy('seat_number')
            ->get();

        return response()->json(['success' => true, 'seats' => $seats]);
    }

    // ✅ Get questions for a seat (based on active poll)
    public function getQuestions($seatId)
    {
        $poll = Poll::where('is_active', 1)->first();
        if (!$poll) {
            return response()->json(['success' => false, 'message' => 'No active poll found.']);
        }

        $questions = PollQuestion::where('poll_id', $poll->id)
            ->orderBy('order_number')
            ->get()
            ->map(function ($q) {
                return [
                    'id' => $q->id,
                    'poll_id' => $q->poll_id,
                    'question' => $q->question,
                    'options' => is_array($q->options) ? $q->options : json_decode($q->options, true),
                ];
            });

        return response()->json(['success' => true, 'questions' => $questions]);
    }

    // ✅ Submit poll response
    public function submitPoll(Request $request)
    {
        try {
            $validated = $request->validate([
                'poll_id' => 'required|exists:polls,id',
                'seat_id' => 'required|exists:assembly_seats,id',
                'answers' => 'required|array',
            ]);

            $ip = $request->ip();
            $userId = auth()->id();
            $pollId = $validated['poll_id'];
            $seatId = $validated['seat_id'];
            $answers = $validated['answers'];

            // Check duplicate vote for this seat and poll
            foreach ($answers as $questionId => $selectedOption) {
                if (PollResponse::alreadyVoted($pollId, $seatId, $questionId, $ip, $userId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'आप पहले ही इस सीट के लिए वोट दे चुके हैं!'
                    ]);
                }
            }

            // Save each answer
            foreach ($answers as $questionId => $selectedOption) {
                $response = PollResponse::create([
                    'poll_id' => $pollId,
                    'question_id' => $questionId,
                    'seat_id' => $seatId,
                    'user_id' => $userId,
                    'selected_option' => $selectedOption,
                    'ip_address' => $ip,
                    'user_agent' => $request->header('User-Agent'),
                ]);

                // Update or create aggregated results
                $this->updatePollResult($pollId, $seatId, $questionId, $selectedOption);
            }

            Log::info("Poll submitted", ['poll_id' => $pollId, 'seat_id' => $seatId, 'ip' => $ip]);

            return response()->json(['success' => true, 'message' => 'आपकी राय सफलतापूर्वक दर्ज कर ली गई!']);

        } catch (\Exception $e) {
            Log::error('Poll submit error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'सबमिट करने में त्रुटि: ' . $e->getMessage()]);
        }
    }

    // Helper: Update poll_result table
    private function updatePollResult($pollId, $seatId, $questionId, $selectedOption)
    {
        $question = PollQuestion::find($questionId);
        $options = json_decode($question->options, true);
        $label = '';
        foreach ($options as $opt) {
            if ($opt['value'] === $selectedOption) {
                $label = $opt['label'];
                break;
            }
        }

        $result = PollResult::where([
            'poll_id' => $pollId,
            'seat_id' => $seatId,
            'question_id' => $questionId,
            'option_key' => $selectedOption,
        ])->first();

        if ($result) {
            $result->increment('votes_count');
        } else {
            PollResult::create([
                'poll_id' => $pollId,
                'seat_id' => $seatId,
                'question_id' => $questionId,
                'option_key' => $selectedOption,
                'option_label' => $label,
                'votes_count' => 1,
                'percentage' => 0,
            ]);
        }

        // Update percentages for this question/seat
        $this->updatePercentages($pollId, $seatId, $questionId);
    }

    private function updatePercentages($pollId, $seatId, $questionId)
    {
        $total = PollResult::where('poll_id', $pollId)
            ->where('seat_id', $seatId)
            ->where('question_id', $questionId)
            ->sum('votes_count');

        if ($total > 0) {
            PollResult::where('poll_id', $pollId)
                ->where('seat_id', $seatId)
                ->where('question_id', $questionId)
                ->update([
                    'percentage' => \DB::raw("(votes_count / {$total}) * 100")
                ]);
        }
    }

    // ✅ Get results for admin
    public function getResults($pollId)
    {
        $results = PollResult::where('poll_id', $pollId)
            ->with(['seat', 'question'])
            ->get()
            ->groupBy('seat_id');

        return response()->json(['success' => true, 'results' => $results]);
    }
}