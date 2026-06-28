@extends('layouts.app')

@section('title', ($district->name ?? 'जिला') . ' - द पब्लिक एक्सप्रेस')

@section('content')
<div class="container my-4">
    <h3 class="border-start border-danger border-4 ps-3 mb-4 text-dark font-weight-bold">
        जिला: {{ $district->name ?? 'स्थानीय ख़बरें' }}
    </h3>

    <div class="row">
        @if(isset($news) && $news->count() > 0)
            @foreach($news as $item)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
                    @else
                        <img src="https://via.placeholder.com/400x200" class="card-img-top" alt="Default Image">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title text-dark font-weight-bold" style="font-size: 1.1rem;">{{ \Illuminate\Support\Str::limit($item->title, 60) }}</h5>
                        <p class="card-text text-muted small">{{ \Illuminate\Support\Str::limit(strip_tags($item->content), 100) }}</p>
                        <a href="{{ route('news.show', $item->slug ?? $item->id) }}" class="btn btn-sm btn-danger rounded-pill px-3">पूरी ख़बर पढ़ें</a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-md-12 text-center py-5">
                <h5 class="text-muted">इस जिले में अभी कोई ख़बर उपलब्ध नहीं है।</h5>
            </div>
        @endif
    </div>
</div>
@endsection