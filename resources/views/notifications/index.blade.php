@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">🔔 Notifications</h1>
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm text-red-600 hover:underline">सभी पढ़ें</button>
        </form>
    </div>
    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notif)
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 {{ $notif->is_read ? 'border-gray-300' : 'border-red-500' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-gray-800">{{ $notif->title }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $notif->message }}</p>
                        @if($notif->link)
                            <a href="{{ $notif->link }}" class="text-xs text-red-600 font-bold mt-2 inline-block">View →</a>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notif->is_read)
                        <form action="{{ route('notifications.mark-read', $notif->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs text-gray-400 hover:text-red-600">Mark as read</button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl p-12 text-center">
            <p class="text-gray-400 text-lg">कोई notification नहीं 📭</p>
        </div>
    @endif
</div>
@endsection