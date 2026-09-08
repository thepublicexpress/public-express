@extends('layouts.admin')

@section('title', 'Reporter Analytics')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">📈 Reporter Analytics</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-chart-line"></i> Track reporter performance and views.
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reporter</th>
                            <th>Total News</th>
                            <th>Total Views</th>
                            <th>Avg Views</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No data available.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection