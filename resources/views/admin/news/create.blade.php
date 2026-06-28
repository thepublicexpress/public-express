@extends('layouts.admin')

@section('title', 'नई खबर लिखें')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h3>📰 नई खबर लिखें</h3>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">&larr; वापस</a>
    </div>

    <div class="card p-4">
        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">खबर का शीर्षक (Title)</label>
                    <input type="text" name="title" class="form-control" required placeholder="यहाँ शीर्षक लिखें...">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">श्रेणी (Category)</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- श्रेणी चुनें --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name_hi ?? $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">मुख्य फोटो (Featured Image)</label>
                    <input type="file" name="featured_image" class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">खबर का विवरण</label>
                    <textarea name="body" class="form-control" rows="8" required placeholder="यहाँ पूरी खबर लिखें..."></textarea>
                </div>

                <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary px-4">🚀 खबर पब्लिश करें</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection