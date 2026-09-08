@extends('layouts.admin')

@section('title', 'Edit News')

@push('styles')
<style>
    /* ===== IMAGE UPLOAD PREVIEW ===== */
    .image-upload-wrapper {
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
        background: #f8fafc;
    }
    .image-upload-wrapper:hover {
        border-color: #c62828;
        background: #fef2f2;
    }
    .image-upload-wrapper.dragover {
        border-color: #c62828;
        background: #fef2f2;
    }
    .image-upload-wrapper .upload-icon {
        font-size: 48px;
        color: #94a3b8;
        display: block;
        margin-bottom: 10px;
    }
    .image-upload-wrapper .upload-text {
        font-size: 16px;
        color: #64748b;
        font-weight: 600;
    }
    .image-upload-wrapper .upload-hint {
        font-size: 13px;
        color: #94a3b8;
        margin-top: 4px;
    }
    
    .image-preview-container {
        position: relative;
        display: inline-block;
        max-width: 100%;
        margin-top: 10px;
    }
    .image-preview-container img {
        max-height: 300px;
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        max-width: 100%;
    }
    .image-preview-container .remove-image {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        transition: all 0.2s;
    }
    .image-preview-container .remove-image:hover {
        transform: scale(1.1);
        background: #b91c1c;
    }
    
    .current-image {
        display: inline-block;
        position: relative;
        margin: 10px 0;
    }
    .current-image img {
        max-height: 150px;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
    }
    .current-image .current-label {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: rgba(0,0,0,0.7);
        color: white;
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 4px;
    }
    
    /* ===== UPLOAD PROGRESS BAR ===== */
    .upload-progress {
        display: none;
        margin-top: 12px;
    }
    .upload-progress.active {
        display: block;
    }
    .upload-progress .progress-bar {
        width: 100%;
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }
    .upload-progress .progress-bar .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #c62828, #ef4444);
        border-radius: 4px;
        transition: width 0.3s ease;
        width: 0%;
    }
    .upload-progress .progress-text {
        font-size: 13px;
        color: #64748b;
        margin-top: 6px;
        text-align: center;
        font-weight: 600;
    }
    .upload-progress .progress-text .percentage {
        color: #c62828;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>✏️ Edit News</h4>
        <div>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash"></i> Delete
            </button>
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data" id="editNewsForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $news->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $news->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label fw-bold">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ old('status', $news->status) == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="summary" class="form-label fw-bold">Summary</label>
                        <textarea class="form-control @error('summary') is-invalid @enderror" 
                                  id="summary" name="summary" rows="2">{{ old('summary', $news->summary) }}</textarea>
                        @error('summary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="body" class="form-label fw-bold">Body <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('body') is-invalid @enderror" 
                                  id="body" name="body" rows="10" required>{{ old('body', $news->body) }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ============================================================ -->
                    <!-- ✅ FEATURED IMAGE UPLOAD WITH PREVIEW & PROGRESS BAR         -->
                    <!-- ============================================================ -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">📸 Featured Image</label>
                        
                        <!-- Current Image -->
                        @if($news->featured_image)
                            <div class="current-image">
                                <img src="{{ asset($news->featured_image) }}" alt="{{ $news->alt_text ?? $news->title }}" id="currentImage">
                                <span class="current-label">Current</span>
                            </div>
                            <br>
                        @endif
                        
                        <!-- Upload Area -->
                        <div class="image-upload-wrapper" id="imageUploadWrapper">
                            <input type="file" class="d-none" id="featured_image" name="featured_image" accept="image/*">
                            <span class="upload-icon">📤</span>
                            <div class="upload-text">Click or Drag & Drop to upload</div>
                            <div class="upload-hint">JPG, PNG, GIF, WebP - Max 5MB</div>
                        </div>
                        
                        <!-- Upload Progress -->
                        <div class="upload-progress" id="uploadProgress">
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFill"></div>
                            </div>
                            <div class="progress-text">
                                Uploading... <span class="percentage" id="progressText">0%</span>
                            </div>
                        </div>
                        
                        <!-- Image Preview -->
                        <div class="image-preview-container" id="imagePreviewContainer" style="display:none;">
                            <img id="imagePreview" src="#" alt="Preview">
                            <button type="button" class="remove-image" onclick="removeImage()">×</button>
                        </div>
                        
                        @error('featured_image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ============================================================ -->
                    <!-- ✅ PUSH NOTIFICATION CHECKBOX                                -->
                    <!-- ============================================================ -->
                    <div class="col-md-12 mb-3">
                        <div class="card bg-light border-0">
                            <div class="card-body py-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="send_notification" 
                                           name="send_notification" value="1" 
                                           {{ old('send_notification', $news->status === 'published') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="send_notification">
                                        🔔 Send Push Notification to all subscribers
                                        <small class="text-muted d-block">
                                            <i class="fas fa-info-circle"></i> 
                                            Notification will be sent to all (Guest + Logged-in Users)
                                        </small>
                                    </label>
                                </div>
                                @if($news->status === 'published')
                                    <div class="mt-1">
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> News is published
                                        </span>
                                        <span class="badge bg-info text-dark">
                                            <i class="fas fa-bell"></i> Update will send notification
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-1">
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock"></i> News is {{ $news->status }}
                                        </span>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-bell-slash"></i> Notification only when published
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- ⚡ OPTIONS: BREAKING & FEATURED                             -->
                    <!-- ============================================================ -->
                    <div class="col-md-12 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_breaking" 
                                           name="is_breaking" value="1" 
                                           {{ old('is_breaking', $news->is_breaking) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_breaking">
                                        🔴 Breaking News
                                        <small class="text-muted d-block">(Separate notification will be sent)</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_featured" 
                                           name="is_featured" value="1" 
                                           {{ old('is_featured', $news->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        ⭐ Featured
                                        <small class="text-muted d-block">(Shows on homepage)</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- 📊 SUBSCRIBER STATS                                          -->
                    <!-- ============================================================ -->
                    @php
                        $totalSubscribers = \App\Models\PushSubscription::where('is_active', true)->count();
                        $guestSubscribers = \App\Models\PushSubscription::where('is_active', true)->whereNull('user_id')->count();
                        $userSubscribers = \App\Models\PushSubscription::where('is_active', true)->whereNotNull('user_id')->count();
                    @endphp
                    
                    <div class="col-md-12 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body py-2">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h5 class="mb-0">{{ $totalSubscribers }}</h5>
                                        <small>Total Subscribers</small>
                                    </div>
                                    <div class="col-4">
                                        <h5 class="mb-0">{{ $guestSubscribers }}</h5>
                                        <small>Guest Users</small>
                                    </div>
                                    <div class="col-4">
                                        <h5 class="mb-0">{{ $userSubscribers }}</h5>
                                        <small>Logged-in Users</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- 🚀 SUBMIT BUTTONS                                            -->
                    <!-- ============================================================ -->
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <button type="submit" class="btn btn-success" name="send_notification" value="1">
                            <i class="fas fa-bell"></i> Update & Send Notification
                        </button>
                        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ✅ DELETE MODAL -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this news?</p>
                    <p class="text-danger"><strong>{{ Str::limit($news->title, 50) }}</strong></p>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.news.destroy', $news) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================================
    // ✅ IMAGE UPLOAD WITH PREVIEW & PROGRESS
    // ============================================================
    
    const imageInput = document.getElementById('featured_image');
    const uploadWrapper = document.getElementById('imageUploadWrapper');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const previewImg = document.getElementById('imagePreview');
    const progressContainer = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    const currentImage = document.getElementById('currentImage');
    
    // Click on wrapper to trigger file input
    uploadWrapper.addEventListener('click', function() {
        imageInput.click();
    });
    
    // Drag and drop
    uploadWrapper.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    uploadWrapper.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    uploadWrapper.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            imageInput.files = e.dataTransfer.files;
            handleImageUpload(imageInput.files[0]);
        }
    });
    
    // File input change
    imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            handleImageUpload(this.files[0]);
        }
    });
    
    function handleImageUpload(file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please upload only image files!');
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File is larger than 5MB. Please choose a smaller file.');
            return;
        }
        
        // Show progress
        progressContainer.classList.add('active');
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 10;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                setTimeout(() => {
                    progressContainer.classList.remove('active');
                }, 500);
            }
            progressFill.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '%';
        }, 100);
        
        // Read and preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'inline-block';
            uploadWrapper.style.display = 'none';
            
            // Hide current image if exists
            if (currentImage) {
                currentImage.style.display = 'none';
            }
            
            // Complete progress
            clearInterval(interval);
            progressFill.style.width = '100%';
            progressText.textContent = '100% - ✅ Upload Complete!';
            setTimeout(() => {
                progressContainer.classList.remove('active');
            }, 500);
        };
        reader.readAsDataURL(file);
    }
    
    // Remove image
    window.removeImage = function() {
        imageInput.value = '';
        previewContainer.style.display = 'none';
        previewImg.src = '';
        uploadWrapper.style.display = 'block';
        progressContainer.classList.remove('active');
        progressFill.style.width = '0%';
        progressText.textContent = '0%';
        
        // Show current image again
        if (currentImage) {
            currentImage.style.display = 'inline-block';
        }
    };
    
    // ============================================================
    // ✅ FORM SUBMIT - Show loading
    // ============================================================
    
    document.getElementById('editNewsForm').addEventListener('submit', function(e) {
        // Optional: Show loading spinner
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            submitBtn.disabled = true;
        }
    });
});
</script>
@endsection