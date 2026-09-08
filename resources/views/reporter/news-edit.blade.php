@extends('layouts.reporter')

@section('title', 'खबर संपादित करें')

@push('styles')
<style>
    /* ===== COMPACT TOOLBAR ===== */
    .editor-toolbar {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px 8px 0 0;
        padding: 6px 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        align-items: center;
        border-bottom: none;
    }
    .editor-toolbar .btn-tool {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .editor-toolbar .btn-tool:hover {
        background: #f1f5f9;
        border-color: #c62828;
        color: #c62828;
    }
    .editor-toolbar .btn-tool i {
        font-size: 12px;
    }
    .editor-toolbar .divider {
        width: 1px;
        height: 24px;
        background: #e2e8f0;
        margin: 0 2px;
    }
    .editor-toolbar .tool-label {
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-right: 2px;
    }
    
    /* ===== EDITOR BODY ===== */
    .editor-body-wrapper {
        border: 2px solid #c62828;
        border-radius: 0 0 8px 8px;
        background: #ffffff;
        position: relative;
        min-height: 350px;
        height: auto;
        max-height: 600px;
        border-top: none;
    }
    .editor-body-wrapper .editor-label {
        position: absolute;
        top: -10px;
        left: 16px;
        background: #ffffff;
        padding: 0 8px;
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        z-index: 5;
    }
    .editor-body-wrapper .editor-label .required {
        color: #c62828;
    }
    
    #bodyEditor {
        min-height: 280px;
        padding: 20px;
        font-family: 'Noto Sans Devanagari', sans-serif;
        font-size: 16px;
        line-height: 1.9;
        color: #0f172a;
        outline: none;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        white-space: pre-wrap;
        word-break: break-word;
        word-wrap: break-word;
        background: #ffffff;
        border-radius: 0 0 8px 8px;
        height: 100%;
    }
    
    /* ===== IMAGE UPLOAD WITH PROGRESS ===== */
    .image-upload-wrapper {
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
        background: #fafbfc;
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
        font-size: 36px;
        color: #94a3b8;
        display: block;
        margin-bottom: 8px;
    }
    .image-upload-wrapper .upload-text {
        font-size: 14px;
        color: #64748b;
    }
    .image-upload-wrapper .upload-hint {
        font-size: 12px;
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
        max-height: 200px;
        border-radius: 8px;
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
        width: 28px;
        height: 28px;
        font-size: 16px;
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
    
    .upload-progress {
        display: none;
        margin-top: 10px;
    }
    .upload-progress.active {
        display: block;
    }
    .upload-progress .progress-bar {
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }
    .upload-progress .progress-bar .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #c62828, #ef4444);
        border-radius: 3px;
        transition: width 0.3s ease;
        width: 0%;
    }
    .upload-progress .progress-text {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
        text-align: center;
    }
    
    /* ===== WORD COUNT ===== */
    .word-count {
        font-size: 13px;
        color: #94a3b8;
        text-align: right;
        margin-top: 6px;
        padding-right: 4px;
        font-weight: 600;
    }
    .word-count .count {
        color: #0f172a;
        font-weight: 700;
    }
    .word-count .target {
        color: #94a3b8;
    }
    .word-count.warning {
        color: #eab308;
    }
    .word-count.danger {
        color: #dc2626;
    }
    .word-count.success {
        color: #16a34a;
    }
    
    /* ===== WORD PROGRESS ===== */
    .word-progress {
        width: 100%;
        height: 4px;
        background: #e2e8f0;
        border-radius: 4px;
        margin-top: 4px;
        overflow: hidden;
    }
    .word-progress .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #c62828, #ef4444);
        border-radius: 4px;
        transition: width 0.3s ease;
        width: 0%;
    }
    
    /* ===== MOBILE FRIENDLY ===== */
    @media (max-width: 640px) {
        .editor-toolbar {
            padding: 4px 6px;
            gap: 3px;
        }
        .editor-toolbar .btn-tool {
            padding: 3px 6px;
            font-size: 11px;
        }
        .editor-toolbar .btn-tool i {
            font-size: 10px;
        }
        .editor-toolbar .tool-label {
            font-size: 9px;
        }
        .editor-body-wrapper {
            min-height: 250px;
            max-height: 400px;
        }
        #bodyEditor {
            min-height: 200px;
            font-size: 15px;
            padding: 14px;
        }
        .card-body {
            padding: 12px !important;
        }
        .image-preview-container img {
            max-height: 150px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-md-3">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-gray-800">✏️ खबर संपादित करें</h4>
                <span class="badge bg-{{ $news->status == 'published' ? 'success' : ($news->status == 'pending' ? 'warning' : ($news->status == 'rejected' ? 'danger' : 'secondary')) }}">
                    {{ ucfirst($news->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">खबर की जानकारी अपडेट करें</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reporter.news.update', $news->id) }}" enctype="multipart/form-data" id="editNewsForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-12 mb-2">
                                <label for="title" class="form-label fw-bold small">शीर्षक <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $news->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category & Type -->
                            <div class="col-md-6 mb-2">
                                <label for="category_id" class="form-label fw-bold small">श्रेणी</label>
                                <select class="form-select form-select-sm @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                    <option value="">श्रेणी चुनें</option>
                                    @foreach($categories as $category)
                                        @php
                                            $selected = old('category_id', $news->category_id) == $category->id;
                                        @endphp
                                        <option value="{{ $category->id }}" {{ $selected ? 'selected' : '' }}>
                                            {{ $category->name }}
                                            @if(!$category->is_active)
                                                <span class="text-muted">(निष्क्रिय)</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="type" class="form-label fw-bold small">प्रकार</label>
                                <select class="form-select form-select-sm @error('type') is-invalid @enderror" id="type" name="type">
                                    <option value="text" {{ old('type', $news->type) == 'text' ? 'selected' : '' }}>📝 टेक्स्ट</option>
                                    <option value="video" {{ old('type', $news->type) == 'video' ? 'selected' : '' }}>🎬 वीडियो</option>
                                    <option value="short" {{ old('type', $news->type) == 'short' ? 'selected' : '' }}>📱 शॉर्ट</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Video URL -->
                            <div class="col-md-12 mb-2" id="video_url_div" style="display: {{ (old('type', $news->type) == 'video' || old('type', $news->type) == 'short') ? 'block' : 'none' }};">
                                <label for="video_url" class="form-label fw-bold small">वीडियो / शॉर्ट URL</label>
                                <input type="url" class="form-control form-control-sm @error('video_url') is-invalid @enderror" 
                                       id="video_url" name="video_url" value="{{ old('video_url', $news->video_url) }}"
                                       placeholder="https://youtube.com/...">
                                @error('video_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div class="col-md-12 mb-2">
                                <label class="form-label fw-bold small">📍 स्थान</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <select class="form-select form-select-sm @error('state_id') is-invalid @enderror" id="state_id" name="state_id">
                                            <option value="">राज्य</option>
                                            @foreach($states as $state)
                                                @php
                                                    $selected = old('state_id') !== null 
                                                        ? old('state_id') == $state->id 
                                                        : (isset($selectedState) && $selectedState == $state->id);
                                                @endphp
                                                <option value="{{ $state->id }}" {{ $selected ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('state_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <select class="form-select form-select-sm @error('district_id') is-invalid @enderror" id="district_id" name="district_id">
                                            <option value="">जिला</option>
                                            @foreach($districts as $district)
                                                @php
                                                    $selected = old('district_id') !== null 
                                                        ? old('district_id') == $district->id 
                                                        : (isset($selectedDistrict) && $selectedDistrict == $district->id);
                                                @endphp
                                                <option value="{{ $district->id }}" {{ $selected ? 'selected' : '' }}>
                                                    {{ $district->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('district_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row g-2 mt-1">
                                    <div class="col-6">
                                        <select class="form-select form-select-sm @error('tehsil_id') is-invalid @enderror" id="tehsil_id" name="tehsil_id">
                                            <option value="">तहसील</option>
                                            @foreach($tehsils as $tehsil)
                                                @php
                                                    $selected = old('tehsil_id') !== null 
                                                        ? old('tehsil_id') == $tehsil->id 
                                                        : (isset($selectedTehsil) && $selectedTehsil == $tehsil->id);
                                                @endphp
                                                <option value="{{ $tehsil->id }}" {{ $selected ? 'selected' : '' }}>
                                                    {{ $tehsil->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tehsil_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <select class="form-select form-select-sm @error('block_id') is-invalid @enderror" id="block_id" name="block_id">
                                            <option value="">ब्लॉक</option>
                                            @foreach($blocks as $block)
                                                @php
                                                    $selected = old('block_id') !== null 
                                                        ? old('block_id') == $block->id 
                                                        : (isset($selectedBlock) && $selectedBlock == $block->id);
                                                @endphp
                                                <option value="{{ $block->id }}" {{ $selected ? 'selected' : '' }}>
                                                    {{ $block->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('block_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="col-md-12 mb-2">
                                <label for="summary" class="form-label fw-bold small">सारांश</label>
                                <textarea class="form-control form-control-sm @error('summary') is-invalid @enderror" 
                                          id="summary" name="summary" rows="2">{{ old('summary', $news->summary) }}</textarea>
                                @error('summary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ============================================================ -->
                            <!-- ✅ EDITOR TOOLBAR                                             -->
                            <!-- ============================================================ -->
                            <div class="col-md-12 mb-1">
                                <label class="form-label fw-bold small">📝 खबर की सामग्री <span class="text-danger">*</span></label>
                                
                                <div class="editor-toolbar">
                                    <span class="tool-label">Format:</span>
                                    <button type="button" class="btn-tool" onclick="formatText('h2')" title="Heading 2"><i class="fas fa-heading"></i> H2</button>
                                    <button type="button" class="btn-tool" onclick="formatText('h3')" title="Heading 3">H3</button>
                                    <button type="button" class="btn-tool" onclick="formatText('h4')" title="Heading 4">H4</button>
                                    <span class="divider"></span>
                                    <button type="button" class="btn-tool" onclick="formatText('bold')" title="Bold"><i class="fas fa-bold"></i> B</button>
                                    <button type="button" class="btn-tool" onclick="formatText('italic')" title="Italic"><i class="fas fa-italic"></i> I</button>
                                    <span class="divider"></span>
                                    <button type="button" class="btn-tool" onclick="formatText('quote')" title="Pull Quote"><i class="fas fa-quote-left"></i> Quote</button>
                                    <button type="button" class="btn-tool" onclick="formatText('list')" title="Bullet List"><i class="fas fa-list"></i> List</button>
                                    <span class="divider"></span>
                                    <button type="button" class="btn-tool" onclick="openInlineImageUpload()" title="Insert Image"><i class="fas fa-image"></i> Image</button>
                                    <span class="divider"></span>
                                    <button type="button" class="btn-tool" onclick="togglePreview()" title="Preview"><i class="fas fa-eye"></i></button>
                                    <button type="button" class="btn-tool" onclick="clearFormatting()" title="Clear Formatting"><i class="fas fa-eraser"></i></button>
                                </div>

                                <!-- Editor Body -->
                                <div class="editor-body-wrapper">
                                    <span class="editor-label">📝 सामग्री <span class="required">*</span></span>
                                    <div id="bodyEditor" contenteditable="true" role="textbox" aria-multiline="true" data-placeholder="खबर लिखें..."></div>
                                    <textarea class="form-control form-control-sm @error('body') is-invalid @enderror" 
                                              id="body" name="body" rows="12" required hidden>{{ old('body', $news->body) }}</textarea>
                                </div>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <div class="word-count" id="wordCount">
                                    <span class="count" id="wordCountNumber">0</span> 
                                    <span class="target">शब्द</span>
                                    <span id="wordStatus" style="margin-left:8px;font-size:11px;"></span>
                                </div>
                                <div class="word-progress">
                                    <div class="progress-fill" id="wordProgressFill" style="width: 0%;"></div>
                                </div>
                            </div>

                            <!-- ============================================================ -->
                            <!-- ✅ IMAGE UPLOAD WITH PREVIEW & PROGRESS                      -->
                            <!-- ============================================================ -->
                            <div class="col-md-12 mb-2" id="featuredImageDiv" style="display: {{ (old('type', $news->type) == 'text') ? 'block' : 'none' }};">
                                <label class="form-label fw-bold small">📸 मुख्य फोटो</label>
                                
                                <!-- Current Image Preview -->
                                @if($news->featured_image)
                                    <div class="mb-2">
                                        <div class="image-preview-container">
                                            <img src="{{ asset($news->featured_image) }}" alt="Current Image" style="max-height: 150px; border-radius: 8px; border: 2px solid #e2e8f0;">
                                            <button type="button" class="remove-image" onclick="removeCurrentImage()" title="Remove current image">×</button>
                                        </div>
                                        <small class="text-muted">वर्तमान फोटो</small>
                                    </div>
                                @endif
                                
                                <!-- Upload Area -->
                                <div class="image-upload-wrapper" id="imageUploadWrapper">
                                    <input type="file" class="d-none" id="featured_image" name="image" accept="image/*">
                                    <span class="upload-icon">📤</span>
                                    <div class="upload-text">फोटो अपलोड करें</div>
                                    <div class="upload-hint">JPG, PNG, WebP - Max 5MB</div>
                                </div>
                                
                                <!-- Upload Progress -->
                                <div class="upload-progress" id="uploadProgress">
                                    <div class="progress-bar">
                                        <div class="progress-fill" id="progressFill"></div>
                                    </div>
                                    <div class="progress-text" id="progressText">0%</div>
                                </div>
                                
                                <!-- New Image Preview -->
                                <div class="image-preview-container" id="newImagePreviewContainer" style="display:none;">
                                    <img id="newImagePreview" src="#" alt="New Featured Image">
                                    <button type="button" class="remove-image" onclick="removeNewImage()">×</button>
                                </div>
                                
                                <input type="hidden" id="remove_current_image" name="remove_current_image" value="0">
                                
                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Options -->
                            <div class="col-md-12 mb-2">
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" id="is_breaking" name="is_breaking" value="1" {{ old('is_breaking', $news->is_breaking) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="is_breaking">🔴 BREAKING</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $news->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="is_featured">⭐ फीचर्ड</label>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-12 mb-2">
                                <label for="status" class="form-label fw-bold small">स्थिति</label>
                                <select class="form-select form-select-sm @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="published" {{ old('status', $news->status) == 'published' ? 'selected' : '' }}>✅ प्रकाशित</option>
                                    <option value="draft" {{ old('status', $news->status) == 'draft' ? 'selected' : '' }}>📄 ड्राफ्ट</option>
                                    <option value="pending" {{ old('status', $news->status) == 'pending' ? 'selected' : '' }}>⏳ समीक्षा के लिए</option>
                                    <option value="rejected" {{ old('status', $news->status) == 'rejected' ? 'selected' : '' }}>❌ अस्वीकृत</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($news->status == 'rejected' && $news->rejection_reason)
                                    <div class="alert alert-danger mt-2 py-1 small">
                                        <strong>अस्वीकृति कारण:</strong> {{ $news->rejection_reason }}
                                    </div>
                                @endif
                            </div>

                            <!-- Submit -->
                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> अपडेट करें
                                </button>
                                <button type="submit" class="btn btn-warning" name="status" value="pending">
                                    <i class="fas fa-paper-plane"></i> समीक्षा के लिए
                                </button>
                                <a href="{{ route('reporter.news.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> रद्द करें
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- ✅ INLINE IMAGE UPLOAD MODAL                                  -->
<!-- ============================================================ -->
<div class="modal fade" id="inlineImageModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">📸 इमेज डालें</h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label small">इमेज अपलोड करें</label>
                    <input type="file" class="form-control form-control-sm" id="inlineImageInput" accept="image/*">
                    <small class="text-muted">JPG, PNG, WebP - Max 2MB</small>
                </div>
                <div class="mb-2">
                    <label class="form-label small">कैप्शन (वैकल्पिक)</label>
                    <input type="text" class="form-control form-control-sm" id="inlineImageCaption" placeholder="इमेज कैप्शन">
                </div>
                <div id="inlineImagePreview" style="display:none; margin-top:8px;">
                    <img id="inlinePreviewImg" src="#" alt="Preview" style="max-height:120px; border-radius:8px; border:2px solid #e2e8f0;">
                </div>
                <div id="inlineUploadProgress" style="display:none; margin-top:8px;">
                    <div class="progress">
                        <div id="inlineProgressBar" class="progress-bar" style="width:0%"></div>
                    </div>
                    <small style="font-size:12px; color:#64748b; display:block; margin-top:4px;" id="inlineUploadStatus">अपलोड हो रहा है...</small>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="insertInlineImage()">
                    <i class="fas fa-plus"></i> डालें
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- ✅ PREVIEW MODAL                                              -->
<!-- ============================================================ -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">👁️ Preview</h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="in-content-preview" id="previewContent"></div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================================
    // ✅ EDITOR FUNCTIONS
    // ============================================================
    
    const editor = document.getElementById('bodyEditor');
    const textarea = document.getElementById('body');
    const wordCountEl = document.getElementById('wordCountNumber');
    const wordProgress = document.getElementById('wordProgressFill');
    const wordStatus = document.getElementById('wordStatus');

    // Set initial content
    if (textarea.value) {
        editor.innerHTML = textarea.value;
    }

    function getWordCount() {
        const text = editor.innerText || '';
        const cleanText = text.replace(/\s+/g, ' ').trim();
        if (!cleanText) return 0;
        return cleanText.split(' ').length;
    }

    function updateWordCount() {
        const count = getWordCount();
        wordCountEl.textContent = count;
        
        const percentage = Math.min((count / 200) * 100, 100);
        wordProgress.style.width = percentage + '%';
        
        const wordCountDiv = document.getElementById('wordCount');
        wordCountDiv.className = 'word-count';
        
        if (count < 200) {
            const remaining = 200 - count;
            wordStatus.textContent = `⏳ ${remaining} और शब्द चाहिए`;
            wordStatus.style.color = '#eab308';
            wordProgress.className = 'progress-fill warning';
            wordCountDiv.classList.add('warning');
        } else if (count >= 200 && count < 250) {
            wordStatus.textContent = '✅ अच्छा!';
            wordStatus.style.color = '#16a34a';
            wordProgress.className = 'progress-fill success';
            wordCountDiv.classList.add('success');
        } else {
            wordStatus.textContent = '✅ बहुत अच्छा!';
            wordStatus.style.color = '#16a34a';
            wordProgress.className = 'progress-fill success';
            wordCountDiv.classList.add('success');
        }
        
        textarea.value = editor.innerHTML;
    }

    function formatText(type) {
        const selection = window.getSelection();
        if (!selection.rangeCount) return;
        
        const range = selection.getRangeAt(0);
        const selectedText = range.extractContents();
        
        let wrapper = document.createElement('span');
        
        switch(type) {
            case 'h2':
                wrapper = document.createElement('h2');
                break;
            case 'h3':
                wrapper = document.createElement('h3');
                break;
            case 'h4':
                wrapper = document.createElement('h4');
                break;
            case 'bold':
                wrapper = document.createElement('strong');
                break;
            case 'italic':
                wrapper = document.createElement('em');
                break;
            case 'quote':
                wrapper = document.createElement('div');
                wrapper.className = 'pull-quote';
                const author = document.createElement('span');
                author.className = 'quote-author';
                author.textContent = '- व्यक्ति का नाम';
                wrapper.appendChild(selectedText);
                wrapper.appendChild(author);
                range.deleteContents();
                range.insertNode(wrapper);
                updateWordCount();
                return;
            case 'list':
                wrapper = document.createElement('ul');
                wrapper.className = 'content-list';
                const items = selectedText.textContent.split('\n').filter(item => item.trim());
                items.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item.trim();
                    wrapper.appendChild(li);
                });
                range.deleteContents();
                range.insertNode(wrapper);
                updateWordCount();
                return;
            default:
                return;
        }
        
        wrapper.appendChild(selectedText);
        range.deleteContents();
        range.insertNode(wrapper);
        updateWordCount();
    }

    function openInlineImageUpload() {
        const modal = new bootstrap.Modal(document.getElementById('inlineImageModal'));
        modal.show();
    }

    function insertInlineImage() {
        const fileInput = document.getElementById('inlineImageInput');
        const caption = document.getElementById('inlineImageCaption').value.trim();
        const file = fileInput.files[0];
        
        if (!file) {
            alert('कृपया एक इमेज चुनें!');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('figure');
            const imgTag = document.createElement('img');
            imgTag.src = e.target.result;
            imgTag.alt = caption || 'Image';
            
            img.appendChild(imgTag);
            
            if (caption) {
                const figcaption = document.createElement('figcaption');
                figcaption.textContent = caption;
                img.appendChild(figcaption);
            }
            
            const selection = window.getSelection();
            const range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(img);
            
            range.setStartAfter(img);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
            
            updateWordCount();
            
            bootstrap.Modal.getInstance(document.getElementById('inlineImageModal')).hide();
        };
        reader.readAsDataURL(file);
    }

    function togglePreview() {
        const previewContent = document.getElementById('previewContent');
        previewContent.innerHTML = editor.innerHTML;
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    }

    function clearFormatting() {
        const selection = window.getSelection();
        if (!selection.rangeCount) return;
        
        const range = selection.getRangeAt(0);
        const selectedText = range.extractContents();
        const textNode = document.createTextNode(selectedText.textContent);
        
        range.deleteContents();
        range.insertNode(textNode);
        updateWordCount();
    }

    // ============================================================
    // ✅ FEATURED IMAGE UPLOAD WITH PROGRESS
    // ============================================================
    
    const imageInput = document.getElementById('featured_image');
    const uploadWrapper = document.getElementById('imageUploadWrapper');
    const progressContainer = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    const newPreviewContainer = document.getElementById('newImagePreviewContainer');
    const newPreviewImg = document.getElementById('newImagePreview');
    
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
            alert('कृपया केवल इमेज फाइल अपलोड करें!');
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('फाइल 5MB से बड़ी है। कृपया छोटी फाइल चुनें।');
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
            newPreviewImg.src = e.target.result;
            newPreviewContainer.style.display = 'inline-block';
            uploadWrapper.style.display = 'none';
            
            // Hide current image if exists
            const currentImg = document.querySelector('.image-preview-container img');
            if (currentImg) {
                currentImg.closest('.image-preview-container').style.display = 'none';
            }
            
            // Complete progress
            clearInterval(interval);
            progressFill.style.width = '100%';
            progressText.textContent = '100% - ✅ अपलोड पूरा!';
            setTimeout(() => {
                progressContainer.classList.remove('active');
            }, 500);
        };
        reader.readAsDataURL(file);
    }
    
    // Remove current image
    window.removeCurrentImage = function() {
        if (confirm('क्या आप वर्तमान फोटो हटाना चाहते हैं?')) {
            document.getElementById('remove_current_image').value = '1';
            const container = document.querySelector('.image-preview-container');
            if (container) {
                container.style.display = 'none';
            }
            // Show upload wrapper
            uploadWrapper.style.display = 'block';
        }
    };
    
    // Remove new image
    window.removeNewImage = function() {
        imageInput.value = '';
        newPreviewContainer.style.display = 'none';
        newPreviewImg.src = '';
        uploadWrapper.style.display = 'block';
        progressContainer.classList.remove('active');
        progressFill.style.width = '0%';
        progressText.textContent = '0%';
        
        // Show current image again if exists
        const currentContainer = document.querySelector('.image-preview-container');
        if (currentContainer && document.getElementById('remove_current_image').value !== '1') {
            currentContainer.style.display = 'inline-block';
        }
    };

    // ============================================================
    // ✅ FORM SUBMIT - Sync and Validate
    // ============================================================
    
    document.getElementById('editNewsForm').addEventListener('submit', function(e) {
        textarea.value = editor.innerHTML;
        const title = document.getElementById('title').value.trim();
        const body = textarea.value.trim();
        const type = document.getElementById('type').value;
        const videoUrl = document.getElementById('video_url').value.trim();
        
        if (!title || !body) {
            e.preventDefault();
            alert('कृपया शीर्षक और खबर भरें।');
            return;
        }
        
        if ((type === 'video' || type === 'short') && !videoUrl) {
            e.preventDefault();
            alert('कृपया वीडियो/शॉर्ट का URL दर्ज करें।');
            return;
        }
    });

    // ============================================================
    // ✅ TYPE CHANGE HANDLER
    // ============================================================
    
    document.getElementById('type').addEventListener('change', function() {
        const type = this.value;
        const videoDiv = document.getElementById('video_url_div');
        const videoInput = document.getElementById('video_url');
        const imageDiv = document.getElementById('featuredImageDiv');
        
        if (type === 'video' || type === 'short') {
            videoDiv.style.display = 'block';
        } else {
            videoDiv.style.display = 'none';
            videoInput.removeAttribute('required');
        }
        
        if (type === 'text') {
            imageDiv.style.display = 'block';
        } else {
            imageDiv.style.display = 'none';
        }
    });

    // ============================================================
    // ✅ INLINE IMAGE PREVIEW
    // ============================================================
    
    document.getElementById('inlineImageInput').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('inlinePreviewImg').src = e.target.result;
            document.getElementById('inlineImagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
        document.getElementById('inlineUploadProgress').style.display = 'block';
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                document.getElementById('inlineUploadStatus').textContent = '✅ तैयार!';
            }
            document.getElementById('inlineProgressBar').style.width = progress + '%';
        }, 200);
    });

    // ============================================================
    // ✅ EDITOR EVENTS
    // ============================================================
    
    editor.addEventListener('input', function() {
        updateWordCount();
        textarea.value = editor.innerHTML;
    });
    editor.addEventListener('keyup', function() {
        updateWordCount();
        textarea.value = editor.innerHTML;
    });
    editor.addEventListener('paste', function(e) {
        setTimeout(function() {
            updateWordCount();
            textarea.value = editor.innerHTML;
        }, 100);
    });

    // ============================================================
    // ✅ INIT
    // ============================================================
    
    updateWordCount();
    textarea.value = editor.innerHTML;

    // Make functions global
    window.formatText = formatText;
    window.openInlineImageUpload = openInlineImageUpload;
    window.insertInlineImage = insertInlineImage;
    window.togglePreview = togglePreview;
    window.clearFormatting = clearFormatting;
});
</script>
@endsection