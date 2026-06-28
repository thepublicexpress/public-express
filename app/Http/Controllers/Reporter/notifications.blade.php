@extends('layouts.reporter')

@section('title', 'Notifications')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>🔔 Notifications</h4>
        <form method="POST" action="{{ route('reporter.notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary">Mark All Read</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($notifications->count() > 0)
        @foreach($notifications as $notif)
        <div class="card mb-2 {{ $notif->is_read ? 'bg-white' : 'bg-light border-primary' }}">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">
                        @if($notif->type == 'news_approved')
                            <span class="text-success">✅</span>
                        @elseif($notif->type == 'news_rejected')
                            <span class="text-danger">❌</span>
                        @else
                            <span class="text-info">📢</span>
                        @endif
                        {{ $notif->title }}
                    </h6>
                    <p class="mb-1 small text-muted" style="white-space: pre-line;">{{ $notif->message }}</p>
                    <small class="text-muted">{{ $notif->time_ago }}</small>
                    @if($notif->news_id)
                        <a href="{{ route('news.show', $notif->news->slug ?? '') }}" class="btn btn-sm btn-link">View News</a>
                    @endif
                </div>
                @if(!$notif->is_read)
                    <form method="POST" action="{{ route('reporter.notifications.mark-read', $notif->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">Mark Read</button>
                    </form>
                @endif
            </div>
        </div>
        @endforeach
        <div class="mt-3 d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="text-center text-muted py-5">
            <i class="fas fa-bell-slash fa-3x mb-3"></i>
            <p>No notifications yet.</p>
        </div>
    @endif
</div>
@endsection