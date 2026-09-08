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
@endsection
