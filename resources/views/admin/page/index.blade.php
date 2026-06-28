@extends('layouts.admin')
@section('title', 'Page Manager')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>📄 Page Manager</h3>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Slug</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pages as $page)
                <tr>
                    <td>{{ $page->title }}</td>
                    <td>{{ $page->slug }}</td>
                    <td>{{ $page->updated_at->format('d-m-Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.pages.edit', $page->slug) }}" class="btn btn-sm btn-primary">✏️ Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection