@extends('layouts.admin')
@section('title', 'Site Settings')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">⚙️ Site Settings</h5>
            <small class="text-muted">Manage website appearance and configuration</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.settings.site.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">साइट का नाम (Site Name)</label>
                            <input type="text" name="site_name" class="form-control" 
                                   value="{{ old('site_name', $settings['site_name'] ?? 'द पब्लिक एक्सप्रेस') }}">
                            <small class="text-muted">जैसे: द पब्लिक एक्सप्रेस</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">थीम कलर (Primary Color)</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: {{ old('primary_color', $settings['primary_color'] ?? '#c62828') }}; width:40px;"></span>
                                <input type="color" name="primary_color" class="form-control form-control-color" 
                                       value="{{ old('primary_color', $settings['primary_color'] ?? '#c62828') }}">
                            </div>
                            <small class="text-muted">ब्रांड कलर चुनें</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">📝 मेटा विवरण (Meta Description)</label>
                            <textarea name="meta_description" class="form-control" rows="3" maxlength="500">{{ old('meta_description', $settings['meta_description'] ?? 'द पब्लिक एक्सप्रेस – हर कस्बे, गाँव और सिटी की खबरें। ताजा हिंदी समाचार, राजनीति, शिक्षा, खेल और मनोरंजन।') }}</textarea>
                            <small class="text-muted">SEO के लिए मेटा विवरण – 160 अक्षरों से अधिक न रखें (recommended), अधिकतम 500 अक्षर</small>
                            <div class="mt-1">
                                <span id="charCount" class="badge bg-secondary">0 / 500</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">साइट लोगो (Site Logo)</label>
                            @if(isset($settings['site_logo']) && $settings['site_logo'] && file_exists(public_path($settings['site_logo'])))
                                <div class="mb-2">
                                    <img src="{{ asset($settings['site_logo']) . '?v=' . time() }}" 
                                         style="max-height:100px; border:1px solid #ddd; border-radius:4px; padding:4px;">
                                </div>
                            @endif
                            <input type="file" name="site_logo" class="form-control" accept="image/png,image/jpeg,image/webp">
                            <small class="text-muted">बेहतर परिणाम के लिए PNG या JPG फॉर्मेंट चुने (Max: 4MB)</small>
                            @error('site_logo')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">फेविकॉन (Favicon)</label>
                            @if(isset($settings['favicon']) && $settings['favicon'] && file_exists(public_path($settings['favicon'])))
                                <div class="mb-2">
                                    <img src="{{ asset($settings['favicon']) . '?v=' . time() }}" 
                                         style="height:32px; width:32px; border:1px solid #ddd; border-radius:4px; padding:2px;">
                                </div>
                            @endif
                            <input type="file" name="favicon" class="form-control" accept="image/png,image/ico,image/jpeg">
                            <small class="text-muted">ब्राउजर टैब में दिखने वाला आइकॉन (Max: 512KB)</small>
                            @error('favicon')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">फूटर टेक्स्ट (Footer Text)</label>
                            <input type="text" name="footer_text" class="form-control" 
                                   value="{{ old('footer_text', $settings['footer_text'] ?? 'पब्लिक की आवाज | हाइपरलोकल') }}">
                            <small class="text-muted">वेबसाइट के फूटर में दिखने वाला टेक्स्ट</small>
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="fw-bold mb-3">🌐 Social Media Links</h6>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-facebook text-primary"></i> Facebook</label>
                            <input type="url" name="facebook_url" class="form-control" 
                                   value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}" 
                                   placeholder="https://facebook.com/yourpage">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-twitter text-info"></i> Twitter</label>
                            <input type="url" name="twitter_url" class="form-control" 
                                   value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}" 
                                   placeholder="https://twitter.com/yourhandle">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-instagram text-danger"></i> Instagram</label>
                            <input type="url" name="instagram_url" class="form-control" 
                                   value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" 
                                   placeholder="https://instagram.com/yourpage">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-youtube text-danger"></i> YouTube</label>
                            <input type="url" name="youtube_url" class="form-control" 
                                   value="{{ old('youtube_url', $settings['youtube_url'] ?? '') }}" 
                                   placeholder="https://youtube.com/yourchannel">
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="fw-bold mb-3">📞 Contact Information</h6>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-envelope"></i> Contact Email</label>
                            <input type="email" name="contact_email" class="form-control" 
                                   value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" 
                                   placeholder="info@thepublicexpress.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-phone"></i> Contact Phone</label>
                            <input type="text" name="contact_phone" class="form-control" 
                                   value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" 
                                   placeholder="+91-XXXXXXXXXX">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Character Counter --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.querySelector('textarea[name="meta_description"]');
        const charCount = document.getElementById('charCount');
        
        if (textarea && charCount) {
            function updateCharCount() {
                const length = textarea.value.length;
                charCount.textContent = length + ' / 500';
                if (length > 160) {
                    charCount.className = 'badge bg-warning';
                } else {
                    charCount.className = 'badge bg-secondary';
                }
                if (length > 500) {
                    charCount.className = 'badge bg-danger';
                }
            }
            
            textarea.addEventListener('input', updateCharCount);
            updateCharCount();
        }
    });
</script>

<style>
    .form-control-color {
        padding: 0.25rem;
        height: 38px;
    }
    .input-group-text {
        border-radius: 0.25rem 0 0 0.25rem;
    }
</style>
@endsection