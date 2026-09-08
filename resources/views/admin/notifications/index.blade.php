@extends('layouts.admin')

@section('title', 'सभी सूचनाएँ')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🔔 सभी सूचनाएँ</h5>
            @if($notifications->where('is_read', 0)->count() > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">सभी पढ़ें</button>
                </form>
            @endif
        </div>
        <div class="card-body">
            @if($notifications->count())
                <div class="list-group">
                    @foreach($notifications as $notif)
                        @php $data = json_decode($notif->data, true); @endphp
                        <a href="{{ $data['url'] ?? '#' }}" class="list-group-item list-group-item-action {{ $notif->is_read ? '' : 'bg-light' }}" onclick="markRead({{ $notif->id }})">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $notif->title }}</strong>
                                    <p class="mb-0 text-muted small">{{ $notif->body }}</p>
                                </div>
                                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-3">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted"></i>
                    <p class="mt-3 text-muted">कोई सूचना नहीं</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function markRead(id){
    fetch('{{ route("admin.notifications.mark-read", "") }}/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(() => {
        window.location.reload();
    });
}
</script>
@endsection