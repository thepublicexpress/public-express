@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')
<div class="container-fluid px-0">

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h5 class="mb-0">📂 News Categories</h5>
            <small class="text-muted">Manage all news categories</small>
        </div>
        <div>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-sm-3">
            <div class="card bg-light text-center py-2">
                <small class="text-muted">Total</small>
                <strong>{{ $categories->count() }}</strong>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card bg-success text-white text-center py-2">
                <small>Active</small>
                <strong>{{ $categories->where('is_active', 1)->count() }}</strong>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card bg-secondary text-white text-center py-2">
                <small>Inactive</small>
                <strong>{{ $categories->where('is_active', 0)->count() }}</strong>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card bg-info text-white text-center py-2">
                <small>Total News</small>
                <strong>{{ $categories->sum('news_count') ?? 0 }}</strong>
            </div>
        </div>
    </div>

    <!-- Categories Table (Desktop) -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name (English)</th>
                            <th class="d-none d-lg-table-cell">Name (Hindi)</th>
                            <th class="d-none d-sm-table-cell">Sort Order</th>
                            <th>Status</th>
                            <th style="width:180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $cat->name }}</strong>
                                @if($cat->icon)
                                    <span class="ms-1">{{ $cat->icon }}</span>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">{{ $cat->name_hi ?? '-' }}</td>
                            <td class="d-none d-sm-table-cell">{{ $cat->sort_order ?? 0 }}</td>
                            <td>
                                @if($cat->is_active)
                                    <span class="badge bg-success">✅ Active</span>
                                @else
                                    <span class="badge bg-secondary">⛔ Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#editCategoryModal{{ $cat->id }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.categories.toggle', $cat) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn {{ $cat->is_active ? 'btn-secondary' : 'btn-success' }}" title="Toggle Status">
                                            <i class="fas {{ $cat->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" style="display:inline" 
                                          onsubmit="return confirm('Delete this category? All news in this category will also be affected.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-folder-open fa-2x d-block mb-2"></i>
                                No categories found. Create your first category!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse($categories as $cat)
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                {{ $cat->name }}
                                @if($cat->icon)
                                    <span>{{ $cat->icon }}</span>
                                @endif
                            </h6>
                            <div class="d-flex flex-wrap gap-2 small text-muted">
                                <span>📝 {{ $cat->name_hi ?? '-' }}</span>
                                <span>🔢 {{ $cat->sort_order ?? 0 }}</span>
                            </div>
                        </div>
                        <div>
                            @if($cat->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                data-bs-target="#editCategoryModal{{ $cat->id }}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form method="POST" action="{{ route('admin.categories.toggle', $cat) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $cat->is_active ? 'btn-secondary' : 'btn-success' }}">
                                {{ $cat->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" style="display:inline" 
                              onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-folder-open fa-2x d-block mb-2"></i>
                    No categories found
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ===== ADD CATEGORY MODAL ===== -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name (English) <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Politics" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name (Hindi) <span class="text-danger">*</span></label>
                        <input type="text" name="name_hi" class="form-control" placeholder="e.g., राजनीति" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (Emoji/Class)</label>
                        <input type="text" name="icon" class="form-control" placeholder="e.g., 🏛️ or fas fa-landmark">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <input type="color" name="color" class="form-control form-control-color" value="#c62828">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" placeholder="1, 2, 3..." value="0">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== EDIT CATEGORY MODALS ===== -->
@foreach($categories as $cat)
<div class="modal fade" id="editCategoryModal{{ $cat->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.categories.update', $cat) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit: {{ $cat->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name (English) <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ $cat->name }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name (Hindi) <span class="text-danger">*</span></label>
                        <input type="text" name="name_hi" value="{{ $cat->name_hi }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (Emoji/Class)</label>
                        <input type="text" name="icon" value="{{ $cat->icon }}" class="form-control" placeholder="e.g., 🏛️">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <input type="color" name="color" class="form-control form-control-color" value="{{ $cat->color ?? '#c62828' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ $cat->sort_order ?? 0 }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" 
                                   {{ $cat->is_active ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection