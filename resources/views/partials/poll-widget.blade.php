@php
    $activePoll = DB::table('polls')->where('is_active', true)->latest()->first();
    if($activePoll) {
        $options = json_decode($activePoll->options);
        $hasVoted = DB::table('poll_votes')->where('poll_id', $activePoll->id)->where('ip_address', request()->ip())->exists();
        
        $results = [];
        if($hasVoted) {
            $totalVotes = DB::table('poll_votes')->where('poll_id', $activePoll->id)->count();
            $voteCounts = DB::table('poll_votes')->where('poll_id', $activePoll->id)->select('option_index', DB::raw('count(*) as count'))->groupBy('option_index')->get()->pluck('count', 'option_index');
            foreach($options as $idx => $opt) {
                $count = $voteCounts[$idx] ?? 0;
                $results[$idx] = $totalVotes > 0 ? round(($count / $totalVotes) * 100) : 0;
            }
        }
    }
@endphp

@if($activePoll)
<div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 my-6" id="poll-container">
    <div class="flex items-center gap-2 mb-4">
        <span class="flex h-2 w-2 rounded-full bg-red-600"></span>
        <h3 class="font-bold text-gray-800">जनमत सर्वेक्षण (Poll)</h3>
    </div>
    
    <p class="font-bold text-gray-700 mb-4 leading-tight text-lg">{{ $activePoll->question }}</p>

    <div id="poll-options" class="space-y-3">
        @foreach($options as $index => $option)
            @if($hasVoted)
                <div class="relative w-full bg-gray-100 rounded-xl h-12 overflow-hidden border border-gray-200">
                    <div class="absolute top-0 left-0 h-full bg-red-100" style="width: {{ $results[$index] }}%"></div>
                    <div class="absolute inset-0 flex justify-between items-center px-4 font-bold text-sm">
                        <span>{{ $option }}</span>
                        <span class="text-brand">{{ $results[$index] }}%</span>
                    </div>
                </div>
            @else
                <button onclick="submitVote({{ $activePoll->id }}, {{ $index }})" 
                    class="w-full text-left p-3 border border-gray-200 rounded-xl hover:border-brand hover:bg-red-50 transition font-bold text-sm text-gray-600">
                    {{ $option }}
                </button>
            @endif
        @endforeach
    </div>
    <p id="poll-msg" class="text-[10px] mt-3 text-center text-gray-400 font-bold uppercase"></p>
</div>

<script>
async function submitVote(pollId, optionIndex) {
    try {
        const res = await fetch('{{ route("poll.vote") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ poll_id: pollId, option_index: optionIndex })
        });
        const data = await res.json();
        
        if(data.success) {
            location.reload(); // Simple reload to show results
        } else {
            document.getElementById('poll-msg').innerText = data.message;
            document.getElementById('poll-msg').classList.add('text-red-600');
        }
    } catch(e) {
        console.error(e);
    }
}
</script>
@endif