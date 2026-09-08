@extends('layouts.admin')

@section('title', 'Fake Views Tracking')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">🕵️ Fake Views Tracking</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> This module helps you detect and manage fake views on news articles.
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>News Title</th>
                            <th>Reporter</th>
                            <th>Views</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No suspicious views found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection