@extends('layouts.admin')
@section('title', 'Edit Page - ' . $page->title)
@section('content')
<div class="card">
    <div class="card-header">
        <h3>✏️ Edit Page: {{ $page->title }}</h3>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-sm btn-secondary">← Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.pages.update', $page->slug) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label>Page Title</label>
                <input type="text" name="title" value="{{ $page->title }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Meta Title (SEO)</label>
                <input type="text" name="meta_title" value="{{ $page->meta_title }}" class="form-control">
                <small class="text-muted">Browser tab title (max 60 characters)</small>
            </div>

            <div class="mb-3">
                <label>Meta Description (SEO)</label>
                <textarea name="meta_description" rows="2" class="form-control">{{ $page->meta_description }}</textarea>
                <small class="text-muted">Search result description (max 160 characters)</small>
            </div>

            <div class="mb-3">
                <label>Page Content</label>
                <textarea name="content" id="content" rows="15" class="form-control">{{ $page->content }}</textarea>
                <small class="text-muted">You can use HTML tags like &lt;h1&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, etc.</small>
            </div>

            <button type="submit" class="btn btn-primary">Save Page</button>
        </form>
    </div>
</div>

<!-- Simple WYSIWYG -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#content', {
        theme: 'snow',
        placeholder: 'Write your page content here...'
    });
    
    // Sync quill content with textarea
    var form = document.querySelector('form');
    var textarea = document.querySelector('#content');
    quill.on('text-change', function() {
        textarea.value = quill.root.innerHTML;
    });
    
    // Set initial content
    quill.root.innerHTML = textarea.value;
</script>
@endsection