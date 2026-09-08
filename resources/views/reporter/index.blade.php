@extends('layouts.reporter')

@section('title', 'मेरी खबरें')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>📰 मेरी खबरें</h4>
        <a href="{{ route('reporter.news.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> नई खबर
        </a>
    </div>

    <!-- Status Counts -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h5 mb-0">{{ $counts['total'] ?? 0 }}</div>
                    <small class="text-muted">कुल</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <div class="h5 mb-0 text-warning">{{ $counts['pending'] ?? 0 }}</div>
                    <small class="text-muted">⏳ लंबित</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-center border-success">
                <div class="card-body">
                    <div class="h5 mb-0 text-success">{{ $counts['published'] ?? 0 }}</div>
                    <small class="text-muted">✅ प्रकाशित</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <div class="h5 mb-0 text-danger">{{ $counts['rejected'] ?? 0 }}</div>
                    <small class="text-muted">❌ अस्वीकृत</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-center border-secondary">
                <div class="card-body">
                    <div class="h5 mb-0 text-secondary">{{ $counts['draft'] ?? 0 }}</div>
                    <small class="text-muted">📝 ड्राफ्ट</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">सभी खबरें</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ लंबित</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>✅ प्रकाशित</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ अस्वीकृत</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>📝 ड्राफ्ट</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">फ़िल्टर करें</button>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('reporter.news.index') }}" class="btn btn-secondary w-100">सभी दिखाएं</a>
                </div>
            </form>
        </div>
    </div>

    <!-- News Table -->
    <div class="card">
        <div class="card-body p-0">
            @if(isset($news) && $news->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>शीर्षक</th>
                            <th class="d-none d-md-table-cell">श्रेणी</th>
                            <th>स्थिति</th>
                            <th class="d-none d-sm-table-cell">व्यूज</th>
                            <th class="d-none d-md-table-cell">तारीख</th>
                            <th>कार्रवाई</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($news as $item)
                        <tr>
                            <td>
                                <a href="{{ route('reporter.news.show', $item->id) }}" class="text-decoration-none">
                                    {{ Str::limit($item->title, 40) }}
                                </a>
                                @if($item->is_breaking)
                                    <span class="badge bg-danger ms-1">BREAKING</span>
                                @endif
                                @if($item->is_featured)
                                    <span class="badge bg-warning text-dark ms-1">FEATURED</span>
                                @endif
                                @if($item->rejection_reason)
                                    <br><small class="text-danger">कारण: {{ Str::limit($item->rejection_reason, 30) }}</small>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">{{ $item->category->name ?? 'N/A' }}</td>
                            <td>
                                @if($item->status == 'published')
                                    <span class="badge bg-success">✅ प्रकाशित</span>
                                @elseif($item->status == 'pending')
                                    <span class="badge bg-warning text-dark">⏳ लंबित</span>
                                @elseif($item->status == 'rejected')
                                    <span class="badge bg-danger">❌ अस्वीकृत</span>
                                @else
                                    <span class="badge bg-secondary">📝 ड्राफ्ट</span>
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">{{ number_format($item->views ?? 0) }}</td>
                            <td class="d-none d-md-table-cell">{{ $item->created_at->format('d-m-Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <!-- View -->
                                    <a href="{{ route('reporter.news.show', $item->id) }}" class="btn btn-info" title="देखें">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- ✅ Edit - Available for ALL news (except soft-deleted) -->
                                    @if(!$item->trashed())
                                        <a href="{{ route('reporter.news.edit', $item->id) }}" class="btn btn-warning" title="संपादित करें">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    
                                    <!-- ✅ Resubmit - Available for published & rejected -->
                                    @if(in_array($item->status, ['published', 'rejected']))
                                        <button type="button" class="btn btn-primary" 
                                                onclick="resubmitNews({{ $item->id }})" 
                                                title="पुनः सबमिट करें">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    @endif
                                    
                                    <!-- Delete -->
                                    <button type="button" class="btn btn-danger" onclick="deleteNews({{ $item->id }})" title="डिलीट करें">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <form id="delete-form-{{ $item->id }}" action="{{ route('reporter.news.destroy', $item->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                
                                <form id="resubmit-form-{{ $item->id }}" action="{{ route('reporter.news.resubmit', $item->id) }}" method="POST" style="display:none;">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $news->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                <p>कोई खबर नहीं मिली</p>
                <a href="{{ route('reporter.news.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> पहली खबर लिखें
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function deleteNews(id) {
    if (confirm('क्या आप इस खबर को डिलीट करना चाहते हैं?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}

function resubmitNews(id) {
    if (confirm('क्या आप इस खबर को पुनः approve के लिए सबमिट करना चाहते हैं?\n\nइससे खबर की स्थिति "लंबित" हो जाएगी और एडमिन द्वारा पुनः समीक्षा की जाएगी।')) {
        document.getElementById('resubmit-form-' + id).submit();
    }
}
</script>
@endsection