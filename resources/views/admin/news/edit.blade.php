@extends('layouts.admin')
@section('title', 'खबर संपादित करें')
@section('content')
<div style="max-width:900px">
    <div class="page-header">
        <h1>खबर संपादित करें</h1>
        <a href="{{ route('admin.news.show', $news) }}" class="btn btn-sm" style="color:#667eea">&larr; वापस</a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger" style="background:#fff5f5;border:1px solid #fc8181;border-radius:8px;padding:16px;margin-bottom:20px;color:#c53030">
        <strong>कृपया इन गलतियों को ठीक करें:</strong>
        <ul style="margin:8px 0 0 20px">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div style="background:#f0fff4;border:1px solid #68d391;border-radius:8px;padding:12px 16px;margin-bottom:20px;color:#276749">
        ✅ {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data" id="adminNewsForm">
        @csrf
        @method('PUT')

        <div class="card">
            <h3 style="margin-bottom:20px;font-size:16px;font-weight:700;color:#2d3748">मूल जानकारी</h3>

            {{-- Title --}}
            <div class="form-group" style="margin-bottom:16px">
                <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;color:#4a5568">शीर्षक *</label>
                <input type="text" name="title" value="{{ old('title', $news->title) }}"
                       class="form-control" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit"
                       placeholder="खबर का शीर्षक" required>
            </div>

            {{-- Summary --}}
            <div class="form-group" style="margin-bottom:16px">
                <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;color:#4a5568">सारांश</label>
                <textarea name="summary" rows="3"
                          class="form-control" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical"
                          placeholder="खबर का संक्षिप्त विवरण">{{ old('summary', $news->summary) }}</textarea>
            </div>

            {{-- Body --}}
            <div class="form-group" style="margin-bottom:16px">
                <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;color:#4a5568">खबर का विवरण *</label>
                <textarea name="body" rows="12"
                          class="form-control" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical"
                          placeholder="खबर की पूरी जानकारी यहाँ लिखें" required>{{ old('body', $news->body) }}</textarea>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom:20px;font-size:16px;font-weight:700;color:#2d3748">वर्गीकरण</h3>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                {{-- Category --}}
                <div class="form-group">
                    <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;color:#4a5568">श्रेणी *</label>
                    <select name="category_id" class="form-control" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px" required>
                        <option value="">-- श्रेणी चुनें --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $news->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name_hi ?? $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Reporter --}}
                <div class="form-group">
                    <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;color:#4a5568">रिपोर्टर</label>
                    <select name="user_id" class="form-control" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
                        <option value="">-- रिपोर्टर चुनें --</option>
                        @foreach($reporters as $reporter)
                        <option value="{{ $reporter->id }}" {{ old('user_id', $news->user_id) == $reporter->id ? 'selected' : '' }}>
                            {{ $reporter->name }} ({{ $reporter->phone ?? $reporter->email }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- State --}}
                <div class="form-group">
                    <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;color:#4a5568">राज्य</label>
                    <select name="state_id" id="state_id" class="form-control" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
                        <option value="">-- राज्य चुनें --</option>
                        @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ old('state_id', $news->state_id) == $state->id ? 'selected' : '' }}>
                            {{ $state->name_hi ?? $state->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- District --}}
                <div class="form-group">
                    <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;color:#4a5568">जिला</label>
                    <select name="district_id" id="district_id" class="form-control" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
                        <option value="">-- जिला चुनें --</option>
                        @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ old('district_id', $news->district_id) == $district->id ? 'selected' : '' }}>
                            {{ $district->name_hi ?? $district->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tehsil --}}
                <div class="form-group">
                    <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;color:#4a5568">तहसील</label>
                    <select name="tehsil_id" id="tehsil_id" class="form-control" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
                        <option value="">-- तहसील चुनें --</option>
                        @foreach($tehsils as $tehsil)
                        <option value="{{ $tehsil->id }}" {{ old('tehsil_id', $news->tehsil_id) == $tehsil->id ? 'selected' : '' }}>
                            {{ $tehsil->name_hi ?? $tehsil->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;color:#4a5568">स्थिति *</label>
                    <select name="status" class="form-control" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px" required>
                        <option value="draft"     {{ old('status', $news->status) == 'draft'     ? 'selected' : '' }}>ड्राफ्ट</option>
                        <option value="pending"   {{ old('status', $news->status) == 'pending'   ? 'selected' : '' }}>लंबित</option>
                        <option value="published" {{ old('status', $news->status) == 'published' ? 'selected' : '' }}>प्रकाशित</option>
                        <option value="rejected"  {{ old('status', $news->status) == 'rejected'  ? 'selected' : '' }}>अस्वीकृत</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom:20px;font-size:16px;font-weight:700;color:#2d3748">मीडिया और विकल्प</h3>

            {{-- Featured Image Section --}}
            <div class="form-group" style="margin-bottom:16px">
                <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;color:#4a5568">मुख्य चित्र</label>
                
                @if($news->featured_image)
                <div style="margin-bottom:12px; display: flex; align-items: center; gap: 12px;" id="adminCurrentImgWrapper">
                    <img src="{{ url('/serve-image/' . urlencode($news->featured_image)) }}?v={{ time() }}"
                         style="height:100px; width:150px; border-radius:8px; object-fit:cover; border:1px solid #e2e8f0"
                         onerror="this.src='/images/default-news.jpg'">
                    <div>
                        <p style="font-size:12px; font-weight:bold; color:#4a5568; margin:0">वर्तमान चित्र लाइव है</p>
                        <p style="font-size:11px; color:#a0aec0; margin:2px 0 0 0">अगर चित्र नहीं बदलना है, तो नीचे कुछ न चुनें।</p>
                    </div>
                </div>
                @endif
                
                <input type="file" name="featured_image" id="adminImageInput" accept="image/*"
                       style="width:100%;padding:12px;border:2px dashed #cbd5e0;border-radius:8px;font-size:13px; background:#f7fafc; cursor:pointer;"
                       onchange="processAdminImage(this)">

                {{-- ⏳ LIVE PROGRESS & LIVE PREVIEW BOX FOR ADMIN --}}
                <div id="adminProgressBox" style="display:none; margin-top:14px; padding:16px; border-radius:8px; border:1px solid #e2e8f0; transition: all 0.3s;">
                    <div style="display:flex; justify-content:between; align-items:center; margin-bottom:8px; justify-content: space-between;">
                        <span id="adminStatusText" style="font-size:13px; font-weight:700; color:#4a5568;">फाइल प्रोसेस हो रही है...</span>
                        <span id="adminStatusPercent" style="font-size:13px; font-weight:700; color:#4c51bf;">0%</span>
                    </div>
                    {{-- Progress Bar Outline --}}
                    <div style="width:100%; bg:#edf2f7; background-color:#edf2f7; border-radius:9999px; height:8px; overflow:hidden;">
                        <div id="adminProgressBar" style="background-color:#4c51bf; height:8px; width:0%; transition: all 0.2s;"></div>
                    </div>
                    {{-- Live Image Preview --}}
                    <div style="text-align:center; margin-top:12px;">
                        <img id="adminLivePreview" style="display:none; max-height:18px; max-height:180px; border-radius:8px; border:1px solid #e2e8f0; margin:0 auto; object-fit:cover;">
                    </div>
                </div>
            </div>

            {{-- Video URL --}}
            <div class="form-group" style="margin-bottom:16px">
                <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;color:#4a5568">वीडियो URL</label>
                <input type="url" name="video_url" value="{{ old('video_url', $news->video_url) }}"
                       class="form-control" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px"
                       placeholder="https://youtube.com/...">
            </div>

            {{-- Toggles --}}
            <div style="display:flex;gap:30px;flex-wrap:wrap">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px">
                    <input type="checkbox" name="is_breaking" value="1" {{ old('is_breaking', $news->is_breaking) ? 'checked' : '' }}
                           style="width:18px;height:18px">
                    🔴 Breaking News
                </label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $news->is_featured) ? 'checked' : '' }}
                           style="width:18px;height:18px">
                    ⭐ Featured News
                </label>
            </div>
        </div>

        {{-- Submit Button Panel --}}
        <div style="display:flex;gap:12px;margin-bottom:40px">
            <button type="submit" id="adminSubmitBtn" class="btn btn-success" style="padding:12px 32px;font-size:15px; font-weight:700;">
                💾 अपडेट करें
            </button>
            <a href="{{ route('admin.news.show', $news) }}" class="btn btn-sm" style="padding:12px 20px;font-size:15px;color:#667eea;border:1px solid #667eea;border-radius:8px;text-decoration:none">
                रद्द करें
            </a>
        </div>
    </form>
</div>

<script>
// ===== IMAGE CONFIRMATION & PROGRESS LOGIC =====
function processAdminImage(input) {
    const progressBox = document.getElementById('adminProgressBox');
    const statusText = document.getElementById('adminStatusText');
    const statusPercent = document.getElementById('adminStatusPercent');
    const progressBar = document.getElementById('adminProgressBar');
    const livePreview = document.getElementById('adminLivePreview');
    const submitBtn = document.getElementById('adminSubmitBtn');

    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Size check (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('❌ चित्र का साइज 2MB से कम होना चाहिए भाई!');
            input.value = '';
            return;
        }

        // Show progress UI & Lock submit button
        progressBox.style.display = 'block';
        progressBox.style.backgroundColor = '#ebf8ff'; // light blue
        progressBox.style.borderColor = '#90cdf4';
        statusText.innerHTML = "⏳ चित्र लोड किया जा रहा है...";
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.6';
        submitBtn.innerHTML = "⏳ चित्र लोड हो रहा है...";

        let progress = 0;
        const interval = setInterval(() => {
            progress += 20;
            progressBar.style.width = progress + '%';
            statusPercent.innerHTML = progress + '%';

            if (progress >= 100) {
                clearInterval(interval);
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    livePreview.src = e.target.result;
                    livePreview.style.display = 'block';
                    
                    // Success Status Change
                    progressBox.style.backgroundColor = '#f0fff4'; // light green
                    progressBox.style.borderColor = '#98d391';
                    statusText.innerHTML = "✅ चित्र सफलतापूर्वक वेरिफाई हो गया! अब अपडेट कर सकते हैं।";
                    statusText.style.color = '#276749';
                    statusPercent.innerHTML = "READY";
                    progressBar.style.backgroundColor = '#38a169'; // dark green
                    
                    // Unlock submit button
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.innerHTML = "💾 अपडेट करें";
                }
                reader.readAsDataURL(file);
            }
        }, 100);
    }
}

// District load on state change
document.getElementById('state_id').addEventListener('change', function() {
    const stateId = this.value;
    const districtSelect = document.getElementById('district_id');
    const tehsilSelect = document.getElementById('tehsil_id');

    districtSelect.innerHTML = '<option value="">-- जिला चुनें --</option>';
    tehsilSelect.innerHTML = '<option value="">-- तहसील चुनें --</option>';

    if (!stateId) return;

    fetch('/api/districts/' + stateId)
        .then(r => r.json())
        .then(data => {
            data.forEach(d => {
                districtSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
            });
        });
});

// Tehsil load on district change
document.getElementById('district_id').addEventListener('change', function() {
    const districtId = this.value;
    const tehsilSelect = document.getElementById('tehsil_id');

    tehsilSelect.innerHTML = '<option value="">-- तहसील चुनें --</option>';

    if (!districtId) return;

    fetch('/api/tehsils/' + districtId)
        .then(r => r.json())
        .then(data => {
            data.forEach(t => {
                tehsilSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`;
            });
        });
});
</script>
@endsection