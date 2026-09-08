@extends('layouts.admin')

@section('title', 'न्यूज़लेटर सब्सक्राइबर')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">📧 न्यूज़लेटर सब्सक्राइबर ({{ $subscribers->total() }})</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>नाम</th>
                            <th>ईमेल</th>
                            <th>IP</th>
                            <th>सब्सक्राइब तारीख</th>
                            <th>स्थिति</th>
                            <th>एक्शन</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td>{{ $loop->iteration + ($subscribers->currentPage() - 1) * $subscribers->perPage() }}</td>
                                <td>{{ $subscriber->name ?? '-' }}</td>
                                <td>{{ $subscriber->email }}</td>
                                <td>{{ $subscriber->ip_address ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($subscriber->created_at)->format('d M Y, h:i A') }}</td>
                                <td>
                                    <span class="badge {{ $subscriber->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $subscriber->is_active ? 'सक्रिय' : 'निष्क्रिय' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.newsletter.toggle', $subscriber->id) }}" class="btn btn-sm {{ $subscriber->is_active ? 'btn-warning' : 'btn-success' }}">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                    <a href="{{ route('admin.newsletter.destroy', $subscriber->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('क्या आप सुनिश्चित हैं?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">कोई सब्सक्राइबर नहीं मिला।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $subscribers->links() }}
        </div>
    </div>
</div>
@endsection