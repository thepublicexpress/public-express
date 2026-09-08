@extends('layouts.admin')

@section('title', 'खबर प्रबंधन')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>📰 खबर प्रबंधन</h4>
            <span class="badge bg-warning text-dark">⏳ लंबित: {{ $counts['pending'] ?? 0 }}</span>
            <span class="badge bg-success">✅ प्रकाशित: {{ $counts['published'] ?? 0 }}</span>
            <span class="badge bg-danger">❌ अस्वीकृत: {{ $counts['rejected'] ?? 0 }}</span>
        </div>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> नई खबर लिखें
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="खोजें..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">सभी स्थितियाँ</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ लंबित</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>✅ प्रकाशित</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ अस्वीकृत</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>📝 ड्राफ्ट</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">सभी श्रेणियाँ</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">फ़िल्टर करें</button>
                </div>
            </form>
        </div>
    </div>

    <!-- News Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($news->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>शीर्षक</th>
                            <th class="d-none d-md-table-cell">रिपोर्टर</th>
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
                                <a href="{{ route('admin.news.show', $item->id) }}" class="text-decoration-none">
                                    {{ Str::limit($item->title, 40) }}
                                </a>
                                @if($item->is_breaking)
                                    <span class="badge bg-danger ms-1">BREAKING</span>
                                @endif
                                @if($item->rejection_reason)
                                    <br><small class="text-danger">कारण: {{ Str::limit($item->rejection_reason, 30) }}</small>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">{{ $item->user->name ?? 'Unknown' }}</td>
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
                                    <a href="{{ route('admin.news.show', $item->id) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($item->status == 'pending')
                                        <!-- Approve Button -->
                                        <form action="{{ route('admin.news.approve', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="return confirm('क्या आप इस खबर को स्वीकृत करना चाहते हैं?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Reject Button with Modal -->
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    
                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-danger" onclick="deleteNews({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">खबर अस्वीकृत करें</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.news.reject', $item->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>कृपया अस्वीकृति का कारण बताएं:</p>
                                                    <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द करें</button>
                                                    <button type="submit" class="btn btn-danger">अस्वीकृत करें</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.news.destroy', $item->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
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
</script>
@endsection