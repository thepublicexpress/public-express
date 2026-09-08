@extends('layouts.admin')

@section('title', 'Reporter Detail')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">👤 Reporter Detail</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-user"></i> Reporter ID: {{ $user ?? 'N/A' }}
            </div>
            <p>Detailed analytics for this reporter will be shown here.</p>
            <a href="{{ route('admin.reporter-views.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>
@endsection