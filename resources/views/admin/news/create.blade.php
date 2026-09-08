@extends('layouts.admin')

@section('title', 'नई खबर लिखें')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h3>📰 नई खबर लिखें</h3>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">&larr; वापस</a>
    </div>

    <div class="card p-4">
        <form id="adminCreateNewsForm" action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">खबर का शीर्षक (Title)</label>
                    <input type="text" name="title" class="form-control" required placeholder="यहाँ शीर्षक लिखें...">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">श्रेणी (Category)</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- श्रेणी चुनें --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name_hi ?? $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">रिपोर्टर चुनें (Reporter)</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- रिपोर्टर चुनें --</option>
                        @foreach($reporters as $reporter)
                            <option value="{{ $reporter->id }}">{{ $reporter->name }} ({{ $reporter->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">स्थिति (Status)</label>
                    <select name="status" class="form-select" required>
                        <option value="published">✅ प्रकाशित</option>
                        <option value="draft">📄 ड्राफ्ट</option>
                        <option value="pending">⏳ समीक्षा के लिए</option>
                        <option value="rejected">❌ अस्वीकृत</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">मुख्य फोटो (Featured Image)</label>
                    <input type="file" name="featured_image" class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">खबर की सामग्री</label>
                    <div class="editor-toolbar mb-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="applyHeading('h2')">H2</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="applyHeading('h3')">H3</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="applyHeading('h4')">H4</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleBold()">B</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleItalic()">I</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="applyList()">• List</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertQuote()">Quote</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openImageUpload()">Image</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="togglePreview()">Preview</button>
                    </div>
                    <div id="bodyEditor" class="form-control" contenteditable="true" style="min-height:250px;background:#fff;border:1px solid #ced4da;border-radius:.375rem;padding:.75rem;" data-placeholder="यहाँ खबर लिखें..."></div>
                    <textarea name="body" id="body" class="form-control mt-2" rows="10" required hidden></textarea>
                    <div class="char-count mt-1" id="charCount">0 characters</div>
                </div>

                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-primary px-4" onclick="submitEditorForm('adminCreateNewsForm')">🚀 खबर पब्लिश करें</button>
                </div>
            </div>
        </form>

        <div class="modal fade" id="imageUploadModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <h6 class="modal-title">📸 इमेज डालें</h6>
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label small">इमेज अपलोड करें</label>
                            <input type="file" class="form-control form-control-sm" id="inContentImage" accept="image/*">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">कैप्शन</label>
                            <input type="text" class="form-control form-control-sm" id="imageCaption" placeholder="इमेज कैप्शन">
                        </div>
                        <div id="imagePreviewContainer" class="image-preview" style="display:none;">
                            <img id="imagePreview" src="" alt="Preview">
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="insertImageFromUpload()">
                            <i class="fas fa-plus"></i> डालें
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
    </div>
</div>

<script src="{{ asset('js/news-editor-shared.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initEditor();

        const inContentInput = document.getElementById('inContentImage');
        if (inContentInput) {
            inContentInput.addEventListener('change', function() {
                loadImagePreview('inContentImage', 'imagePreview', 'imagePreviewContainer');
            });
        }
    });
</script>
@endsection