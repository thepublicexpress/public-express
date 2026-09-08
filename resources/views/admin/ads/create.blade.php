@extends('layouts.admin')

@section('title', 'Create Ad - द पब्लिक एक्सप्रेस')

@push('styles')
<style>
    .form-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid #eef2f6;
    }
    .form-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-section-title i {
        color: #c62828;
    }
    .form-label-premium {
        font-weight: 600;
        font-size: 13px;
        color: #1e293b;
        margin-bottom: 4px;
        display: block;
    }
    .form-label-premium .required {
        color: #c62828;
        margin-left: 2px;
    }
    .form-control-premium {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #ffffff;
        color: #0f172a;
    }
    .form-control-premium:focus {
        border-color: #c62828;
        box-shadow: 0 0 0 3px rgba(198, 40, 40, 0.08);
        outline: none;
    }
    .form-control-premium.error {
        border-color: #dc2626;
    }
    .form-control-premium::placeholder {
        color: #94a3b8;
    }
    select.form-control-premium {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
    }
    .form-hint {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 4px;
        display: block;
    }
    .form-error {
        font-size: 12px;
        color: #dc2626;
        margin-top: 4px;
        display: block;
    }
    
    .btn-premium-primary {
        background: #c62828;
        border: none;
        color: white;
        font-weight: 700;
        padding: 10px 32px;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-premium-primary:hover {
        background: #b71c1c;
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(198, 40, 40, 0.3);
    }
    .btn-premium-secondary {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-premium-secondary:hover {
        background: #e2e8f0;
        color: #0f172a;
        text-decoration: none;
    }
    
    .image-upload-box {
        border: 2px dashed #e2e8f0;
        border-radius: 10px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fafbfc;
        position: relative;
    }
    .image-upload-box:hover {
        border-color: #c62828;
        background: #fef2f2;
    }
    .image-upload-box .upload-icon {
        font-size: 32px;
        color: #94a3b8;
        display: block;
        margin-bottom: 8px;
    }
    .image-upload-box .upload-text {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }
    .image-upload-box .upload-hint {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 4px;
    }
    .image-upload-box input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }
    .image-preview-container {
        display: none;
        margin-top: 12px;
        position: relative;
    }
    .image-preview-container img {
        max-height: 150px;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
    }
    .image-preview-container .remove-image {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* ===== MOBILE RESPONSIVE ===== */
    @media (max-width: 640px) {
        .form-section {
            padding: 14px;
        }
        .form-control-premium {
            font-size: 15px;
            padding: 10px 12px;
        }
        .btn-premium-primary,
        .btn-premium-secondary {
            width: 100%;
            justify-content: center;
        }
        .flex-actions {
            flex-direction: column;
        }
        .grid-cols-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-slate-900">📢 Create New Ad</h4>
            <p class="text-muted small">Add a new advertisement to your website</p>
        </div>
        <a href="{{ route('admin.ads.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card premium-card">
        <div class="card-body">
            <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data" id="adForm">
                @csrf

                <!-- ============================================================ -->
                <!-- SECTION 1: BASIC INFORMATION                                  -->
                <!-- ============================================================ -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i> Basic Information
                    </div>
                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-12">
                            <label class="form-label-premium">Ad Title <span class="required">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                   class="form-control-premium @error('title') error @enderror"
                                   placeholder="Enter ad title (e.g., Summer Sale Banner)">
                            @error('title')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Type & Position -->
                        <div class="col-md-6">
                            <label class="form-label-premium">Ad Type <span class="required">*</span></label>
                            <select name="type" required class="form-control-premium @error('type') error @enderror">
                                <option value="">Select Type</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-premium">Ad Position <span class="required">*</span></label>
                            <select name="position" required class="form-control-premium @error('position') error @enderror">
                                <option value="">Select Position</option>
                                @foreach($positions as $key => $label)
                                    <option value="{{ $key }}" {{ old('position') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('position')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- SECTION 2: AD CONTENT                                        -->
                <!-- ============================================================ -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-image"></i> Ad Content
                    </div>
                    <div class="row g-3">
                        <!-- Image Upload -->
                        <div class="col-md-6">
                            <label class="form-label-premium">Ad Image</label>
                            <div class="image-upload-box" id="imageUploadBox">
                                <span class="upload-icon">🖼️</span>
                                <div class="upload-text">Click to upload image</div>
                                <div class="upload-hint">JPG, PNG, WebP (Max 2MB)</div>
                                <input type="file" name="image" accept="image/*" id="adImage">
                            </div>
                            <div class="image-preview-container" id="imagePreviewContainer">
                                <img id="imagePreview" src="#" alt="Preview">
                                <button type="button" class="remove-image" onclick="removeImage()">×</button>
                            </div>
                            @error('image')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- URL -->
                        <div class="col-md-6">
                            <label class="form-label-premium">Ad URL</label>
                            <input type="url" name="url" value="{{ old('url') }}"
                                   class="form-control-premium @error('url') error @enderror"
                                   placeholder="https://example.com/landing-page">
                            <span class="form-hint">Where users will go when they click the ad</span>
                            @error('url')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Code (for AdSense/Media.net) -->
                        <div class="col-12">
                            <label class="form-label-premium">Ad Code</label>
                            <textarea name="code" rows="3"
                                      class="form-control-premium @error('code') error @enderror"
                                      placeholder="Paste your AdSense or Media.net code here">{{ old('code') }}</textarea>
                            <span class="form-hint">Only used for "Code Ad" type</span>
                            @error('code')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label-premium">Description</label>
                            <textarea name="description" rows="2"
                                      class="form-control-premium @error('description') error @enderror"
                                      placeholder="Brief description of the ad (for internal reference)">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- SECTION 3: LOCATION TARGETING                                -->
                <!-- ============================================================ -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-location-dot"></i> Location Targeting
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-premium">State</label>
                            <select name="state_id" class="form-control-premium @error('state_id') error @enderror" id="stateSelect">
                                <option value="">All States</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                            <span class="form-hint">Target a specific state</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-premium">District</label>
                            <select name="district_id" class="form-control-premium @error('district_id') error @enderror" id="districtSelect">
                                <option value="">All Districts</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                @endforeach
                            </select>
                            <span class="form-hint">Target a specific district</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-premium">Tehsil</label>
                            <select name="tehsil_id" class="form-control-premium @error('tehsil_id') error @enderror" id="tehsilSelect">
                                <option value="">All Tehsils</option>
                                @foreach($tehsils as $tehsil)
                                    <option value="{{ $tehsil->id }}" {{ old('tehsil_id') == $tehsil->id ? 'selected' : '' }}>{{ $tehsil->name }}</option>
                                @endforeach
                            </select>
                            <span class="form-hint">Target a specific tehsil</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-premium">Block</label>
                            <select name="block_id" class="form-control-premium @error('block_id') error @enderror" id="blockSelect">
                                <option value="">All Blocks</option>
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                                @endforeach
                            </select>
                            <span class="form-hint">Target a specific block</span>
                        </div>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- SECTION 4: SCHEDULE & SETTINGS                               -->
                <!-- ============================================================ -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-calendar"></i> Schedule & Settings
                    </div>
                    <div class="row g-3">
                        <!-- Schedule -->
                        <div class="col-md-6">
                            <label class="form-label-premium">Start Date</label>
                            <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
                                   class="form-control-premium @error('start_date') error @enderror">
                            <span class="form-hint">Leave blank for immediate start</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-premium">End Date</label>
                            <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                                   class="form-control-premium @error('end_date') error @enderror">
                            <span class="form-hint">Leave blank for no end date</span>
                            @error('end_date')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status & Priority -->
                        <div class="col-md-6">
                            <label class="form-label-premium">Status <span class="required">*</span></label>
                            <select name="status" required class="form-control-premium @error('status') error @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>⛔ Inactive</option>
                                <option value="paused" {{ old('status') == 'paused' ? 'selected' : '' }}>⏸️ Paused</option>
                            </select>
                            @error('status')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-premium">Priority</label>
                            <input type="number" name="priority" value="{{ old('priority', 0) }}"
                                   class="form-control-premium @error('priority') error @enderror"
                                   min="0" max="100" placeholder="0">
                            <span class="form-hint">Higher = appears first (0-100)</span>
                            @error('priority')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- ✅ SUBMIT BUTTONS                                            -->
                <!-- ============================================================ -->
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <button type="submit" class="btn-premium-primary">
                        <i class="fas fa-save"></i> Create Ad
                    </button>
                    <button type="reset" class="btn-premium-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <a href="{{ route('admin.ads.index') }}" class="btn-premium-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ============================================================
// ✅ IMAGE UPLOAD PREVIEW
// ============================================================

document.getElementById('adImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
            document.getElementById('imageUploadBox').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});

function removeImage() {
    document.getElementById('adImage').value = '';
    document.getElementById('imagePreviewContainer').style.display = 'none';
    document.getElementById('imageUploadBox').style.display = 'block';
}

// ============================================================
// ✅ LOCATION DROPDOWN CHAINING
// ============================================================

document.getElementById('stateSelect').addEventListener('change', function() {
    const stateId = this.value;
    const districtSelect = document.getElementById('districtSelect');
    const tehsilSelect = document.getElementById('tehsilSelect');
    const blockSelect = document.getElementById('blockSelect');
    
    // Reset dependent dropdowns
    districtSelect.innerHTML = '<option value="">All Districts</option>';
    tehsilSelect.innerHTML = '<option value="">All Tehsils</option>';
    blockSelect.innerHTML = '<option value="">All Blocks</option>';
    
    if (!stateId) return;
    
    fetch(`/admin/api/districts/${stateId}`)
        .then(response => response.json())
        .then(data => {
            data.forEach(district => {
                districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
            });
        })
        .catch(error => console.error('Error loading districts:', error));
});

document.getElementById('districtSelect').addEventListener('change', function() {
    const districtId = this.value;
    const tehsilSelect = document.getElementById('tehsilSelect');
    const blockSelect = document.getElementById('blockSelect');
    
    tehsilSelect.innerHTML = '<option value="">All Tehsils</option>';
    blockSelect.innerHTML = '<option value="">All Blocks</option>';
    
    if (!districtId) return;
    
    fetch(`/admin/api/tehsils/${districtId}`)
        .then(response => response.json())
        .then(data => {
            data.forEach(tehsil => {
                tehsilSelect.innerHTML += `<option value="${tehsil.id}">${tehsil.name}</option>`;
            });
        })
        .catch(error => console.error('Error loading tehsils:', error));
});

document.getElementById('tehsilSelect').addEventListener('change', function() {
    const tehsilId = this.value;
    const blockSelect = document.getElementById('blockSelect');
    
    blockSelect.innerHTML = '<option value="">All Blocks</option>';
    
    if (!tehsilId) return;
    
    fetch(`/admin/api/blocks/${tehsilId}`)
        .then(response => response.json())
        .then(data => {
            data.forEach(block => {
                blockSelect.innerHTML += `<option value="${block.id}">${block.name}</option>`;
            });
        })
        .catch(error => console.error('Error loading blocks:', error));
});

// ============================================================
// ✅ FORM VALIDATION
// ============================================================

document.getElementById('adForm').addEventListener('submit', function(e) {
    const type = document.querySelector('select[name="type"]').value;
    const image = document.getElementById('adImage').files[0];
    const code = document.querySelector('textarea[name="code"]').value.trim();
    
    if (type === 'image' && !image) {
        e.preventDefault();
        alert('कृपया Image Ad के लिए एक इमेज अपलोड करें।');
        return;
    }
    
    if (type === 'code' && !code) {
        e.preventDefault();
        alert('कृपया Code Ad के लिए कोड डालें।');
        return;
    }
});

console.log('✅ Ad Create Form Loaded');
</script>
@endsection