@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h1 class="mb-1">Opinion Poll</h1><p class="text-muted mb-0">पोल बनाएं, active poll नियंत्रित करें और responses की report देखें।</p></div>
        <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#pollForm">+ नया पोल</button>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="collapse mb-4" id="pollForm"><div class="card card-body"><h5>नया पोल बनाएं</h5>
        <form method="POST" action="{{ route('admin.poll.store') }}">@csrf
            @include('admin.partials.poll-form', ['poll' => null])
            <button class="btn btn-success">पोल सेव करें</button>
        </form>
    </div></div>

    <div class="row mb-4">
        <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body"><h6>कुल responses</h6><h2>{{ $totalVotes }}</h2></div></div></div>
        <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body"><h6>कुल सवाल</h6><h2>{{ $totalQuestions }}</h2></div></div></div>
        <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body"><h6>कुल polls</h6><h2>{{ $polls->count() }}</h2></div></div></div>
        <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body"><h6>active poll</h6><h2>{{ $activePolls }}</h2></div></div></div>
    </div>

    <div class="card mb-4"><div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">रिपोर्ट फ़िल्टर</h5><a class="btn btn-success btn-sm" href="{{ route('admin.poll.export', $filters) }}">CSV डाउनलोड</a></div><div class="card-body">
        <form method="GET" action="{{ route('admin.poll.results') }}" class="row g-3">
            <div class="col-md-3"><label class="form-label">Poll</label><select name="poll_id" class="form-select"><option value="">सभी polls</option>@foreach($polls as $poll)<option value="{{ $poll->id }}" @selected(($filters['poll_id'] ?? '') == $poll->id)>{{ $poll->title }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">जिला</label><select name="district" id="reportDistrict" class="form-select"><option value="">सभी जिले</option>@foreach($districts as $district)<option value="{{ $district }}" @selected(($filters['district'] ?? '') === $district)>{{ $district }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label">विधानसभा</label><select name="seat_id" id="reportSeat" class="form-select"><option value="">सभी विधानसभा</option>@foreach($seats as $seat)<option value="{{ $seat->id }}" data-district="{{ $seat->district }}" @selected(($filters['seat_id'] ?? '') == $seat->id)>{{ $seat->seat_number }} - {{ $seat->seat_name }} ({{ $seat->district }})</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">नाम</label><input type="search" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control" placeholder="नाम खोजें"></div>
            <div class="col-md-2"><label class="form-label">मोबाइल</label><input type="search" name="mobile" value="{{ $filters['mobile'] ?? '' }}" class="form-control" placeholder="मोबाइल खोजें"></div>
            <div class="col-md-2"><label class="form-label">From date</label><input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control"></div>
            <div class="col-md-2"><label class="form-label">To date</label><input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control"></div>
            <div class="col-md-8 d-flex align-items-end gap-2"><button class="btn btn-primary">रिपोर्ट दिखाएँ</button><a class="btn btn-outline-secondary" href="{{ route('admin.poll.results') }}">फ़िल्टर हटाएँ</a></div>
        </form>
    </div></div>

    <div class="card mb-4"><div class="card-header"><h5 class="mb-0">ग्राफ</h5></div><div class="card-body">
        <h6>विधानसभा-wise respondents</h6><canvas id="pollSeatChart" height="100"></canvas>
        @forelse($voteChartData as $index => $voteChart)
            <h6 class="mt-4">{{ $voteChart['title'] }}</h6><canvas id="pollVoteChart{{ $index }}" height="100"></canvas>
        @empty
            <p class="text-muted mb-0">इस filter में vote graph के लिए data नहीं है।</p>
        @endforelse
    </div></div>

    <div class="card mb-4"><div class="card-header"><h5 class="mb-0">पोल मैनेज करें</h5></div><div class="table-responsive">
        <table class="table table-hover mb-0"><thead><tr><th>Title</th><th>सवाल</th><th>Responses</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        @forelse($polls as $poll)
            <tr><td><strong>{{ $poll->title }}</strong><br><small class="text-muted">{{ $poll->description }}</small></td><td>{{ $poll->questions->count() }}</td><td>{{ $poll->responses_count }}</td><td><span class="badge bg-{{ $poll->is_active ? 'success' : 'secondary' }}">{{ $poll->is_active ? 'Active' : 'Inactive' }}</span></td><td class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#editPoll{{ $poll->id }}">Edit</button>
                <form method="POST" action="{{ route('admin.poll.toggle', $poll) }}">@csrf<button class="btn btn-sm btn-outline-warning">{{ $poll->is_active ? 'बंद करें' : 'Active करें' }}</button></form>
                <form method="POST" action="{{ route('admin.poll.destroy', $poll) }}" onsubmit="return confirm('यह पोल हटाएं?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
            </td></tr>
            <tr class="collapse" id="editPoll{{ $poll->id }}"><td colspan="5"><form method="POST" action="{{ route('admin.poll.update', $poll) }}">@csrf @method('PUT') @include('admin.partials.poll-form', ['poll' => $poll])<button class="btn btn-primary">Update poll</button></form></td></tr>
        @empty
            <tr><td colspan="5" class="text-center py-4">अभी कोई poll नहीं बना है।</td></tr>
        @endforelse
        </tbody></table>
    </div></div>

    <div class="card mb-4"><div class="card-header"><h5 class="mb-0">विधानसभा-wise रिपोर्ट</h5></div><div class="table-responsive">
        <table class="table table-striped mb-0"><thead><tr><th>Poll</th><th>जिला</th><th>विधानसभा</th><th>Unique respondents</th></tr></thead><tbody>
        @forelse($seatSummaries as $summary)
            <tr><td>{{ $summary->poll->title ?? '-' }}</td><td>{{ $summary->seat->district ?? '-' }}</td><td>{{ $summary->seat->seat_name ?? '-' }}</td><td><strong>{{ $summary->respondent_count }}</strong></td></tr>
        @empty
            <tr><td colspan="4" class="text-center py-4">अभी कोई विधानसभा-wise response नहीं है।</td></tr>
        @endforelse
        </tbody></table>
    </div></div>

    <div class="card mb-4"><div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"><h5 class="mb-0">Respondent details ({{ $respondents->total() }})</h5><div class="d-flex align-items-center gap-3" aria-label="Respondent columns दिखाएँ या छिपाएँ"><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" role="switch" id="showRespondentName" checked><label class="form-check-label fw-semibold" for="showRespondentName">नाम दिखाएँ</label></div><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" role="switch" id="showRespondentMobile" checked><label class="form-check-label fw-semibold" for="showRespondentMobile">मोबाइल दिखाएँ</label></div></div></div><div class="table-responsive">
        <table class="table table-hover mb-0"><thead><tr><th class="respondent-name-column">नाम</th><th class="respondent-mobile-column">मोबाइल</th><th>जिला</th><th>विधानसभा</th><th>Poll</th><th>Submitted</th></tr></thead><tbody>
        @forelse($respondents as $respondent)
            <tr><td class="respondent-name-column">{{ $respondent->respondent_name ?? '-' }}</td><td class="respondent-mobile-column">{{ $respondent->respondent_mobile ?? '-' }}</td><td>{{ $respondent->seat->district ?? '-' }}</td><td>{{ $respondent->seat->seat_name ?? '-' }}</td><td>{{ $respondent->poll->title ?? '-' }}</td><td>{{ optional($respondent->created_at)->format('d-m-Y H:i') }}</td></tr>
        @empty
            <tr><td colspan="6" class="text-center py-4">इस filter में कोई respondent नहीं मिला।</td></tr>
        @endforelse
        </tbody></table>
    </div><div class="card-footer">{{ $respondents->links() }}</div></div>

    <div class="card"><div class="card-header"><h5 class="mb-0">वोट रिपोर्ट</h5></div><div class="table-responsive">
        <table class="table table-striped mb-0"><thead><tr><th>Poll</th><th>Seat</th><th>Question</th><th>Option</th><th>Votes</th><th>प्रतिशत</th></tr></thead><tbody>
        @forelse($results->flatten() as $result)
            <tr><td>{{ $result->poll->title ?? '-' }}</td><td>{{ $result->seat->seat_name ?? '-' }}</td><td>{{ $result->question->question ?? '-' }}</td><td>{{ $result->option_label }}</td><td>{{ $result->votes_count }}</td><td>{{ round($result->percentage, 1) }}%</td></tr>
        @empty
            <tr><td colspan="6" class="text-center py-4">अभी कोई response नहीं है।</td></tr>
        @endforelse
        </tbody></table>
    </div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const districtFilter = document.getElementById('reportDistrict');
    const seatFilter = document.getElementById('reportSeat');
    const selectedSeat = @json($filters['seat_id'] ?? '');
    function filterReportSeats() {
        const district = districtFilter.value;
        Array.from(seatFilter.options).forEach((option, index) => {
            if (index === 0) return;
            option.hidden = Boolean(district && option.dataset.district !== district);
        });
        if (seatFilter.selectedOptions[0]?.hidden) seatFilter.value = '';
    }
    districtFilter?.addEventListener('change', filterReportSeats);
    filterReportSeats();
    if (selectedSeat) seatFilter.value = selectedSeat;

    new Chart(document.getElementById('pollSeatChart'), {
        type: 'bar',
        data: { labels: @json($chartData['labels']), datasets: [{ label: 'Unique respondents', data: @json($chartData['values']), backgroundColor: '#2563eb', borderRadius: 5 }] },
        options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { display: false } } }
    });

    @foreach($voteChartData as $index => $voteChart)
        new Chart(document.getElementById('pollVoteChart{{ $index }}'), {
            type: 'bar',
            data: { labels: @json($voteChart['labels']), datasets: [{ label: 'Votes', data: @json($voteChart['values']), backgroundColor: ['#2563eb', '#16a34a', '#dc2626', '#f59e0b', '#7c3aed', '#0891b2'], borderRadius: 5 }] },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const percentages = @json($voteChart['percentages']);
                                return ` ${context.raw} votes (${percentages[context.dataIndex]}%)`;
                            }
                        }
                    }
                }
            }
        });
    @endforeach

    function toggleReportColumn(checkboxId, columnClass) {
        const checkbox = document.getElementById(checkboxId);
        const update = () => document.querySelectorAll('.' + columnClass).forEach((cell) => cell.style.display = checkbox.checked ? '' : 'none');
        checkbox?.addEventListener('change', update);
        update();
    }
    toggleReportColumn('showRespondentName', 'respondent-name-column');
    toggleReportColumn('showRespondentMobile', 'respondent-mobile-column');
</script>
@endsection
