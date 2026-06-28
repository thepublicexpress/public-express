<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>नयी खबर लिखें - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background-color: #f3f4f6; }
        .bg-brand { background-color: #c62828; }
        .text-brand { color: #c62828; }
        .form-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; font-size: 14px; color: #374151; margin-bottom: 6px; display: block; }
        .form-input { width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 12px; outline: none; transition: all 0.2s; font-size: 15px; background-color: #f9fafb; }
        .form-input:focus { border-color: #c62828; background-color: white; box-shadow: 0 0 0 4px rgba(198,40,40,0.1); }
        .form-input:disabled { background-color: #e5e7eb; cursor: not-allowed; }
    </style>
</head>
<body class="pb-24">

    <header class="bg-brand text-white p-4 sticky top-0 z-50 shadow-md flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('reporter.news.index') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-xl font-bold">नयी खबर लिखें</h1>
        </div>
    </header>

    <main class="max-w-2xl mx-auto p-4 mt-2">
        <!-- Location & Category Info Alert -->
        @if(isset($locationData) && $locationData['type'] != 'all' && $locationData['type'] != 'national')
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-4">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle"></i>
                    {{ $locationData['message'] ?? 'You have location restrictions.' }}
                </p>
            </div>
        @endif

        @if(isset($locationData) && $locationData['type'] == 'national')
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-4">
                <p class="text-sm text-green-700">
                    <i class="fas fa-globe"></i>
                    {{ $locationData['message'] ?? 'You are a National Reporter. Location is optional.' }}
                </p>
            </div>
        @endif

        @if($categories->count() == 0)
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-4">
                <p class="text-sm text-red-700">
                    <i class="fas fa-exclamation-triangle"></i>
                    No categories assigned to you. Please contact admin.
                </p>
            </div>
        @endif

        <div class="form-card">
            <form action="{{ route('reporter.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Category -->
                <div>
                    <label class="form-label">कैटेगरी चुनें <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="form-input">
                        <option value="">-- चुनें --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @if($categories->count() > 0 && $categories->count() < 10)
                        <small class="text-gray-500">You can only post in assigned categories.</small>
                    @endif
                </div>

                <!-- Location - For National Reporter (Optional) -->
                @if(isset($locationData) && $locationData['type'] == 'national')
                    <div>
                        <label class="form-label">राज्य (वैकल्पिक - National Reporter)</label>
                        <select name="state_id" id="stateSelect" class="form-input" onchange="loadDistricts(this.value)">
                            <option value="">-- राज्य चुनें (वैकल्पिक) --</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-gray-500">आप National Reporter हैं, राज्य चुनना जरूरी नहीं है।</small>
                    </div>

                    <div>
                        <label class="form-label">जिला</label>
                        <select name="district_id" id="districtSelect" class="form-input" onchange="loadTehsils(this.value)">
                            <option value="">-- पहले राज्य चुनें --</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">तहसील/ब्लॉक</label>
                        <select name="tehsil_id" id="tehsilSelect" class="form-input">
                            <option value="">-- पहले जिला चुनें --</option>
                        </select>
                    </div>

                <!-- Location - For State Reporter -->
                @elseif(isset($locationData) && $locationData['type'] == 'state')
                    <div>
                        <label class="form-label">राज्य <span class="text-red-500">*</span></label>
                        <input type="text" class="form-input" value="{{ $locationData['state']->name ?? 'Your Assigned State' }}" disabled>
                        <input type="hidden" name="state_id" value="{{ $locationData['state_id'] ?? '' }}">
                        <small class="text-gray-500">You can only post in your assigned state.</small>
                    </div>

                    <div>
                        <label class="form-label">जिला <span class="text-red-500">*</span></label>
                        <select name="district_id" id="districtSelect" required class="form-input" onchange="loadTehsils(this.value)">
                            <option value="">-- जिला चुनें --</option>
                            @foreach($locationData['districts'] ?? [] as $district)
                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">तहसील/ब्लॉक</label>
                        <select name="tehsil_id" id="tehsilSelect" class="form-input">
                            <option value="">-- पहले जिला चुनें --</option>
                        </select>
                    </div>

                <!-- Location - For District Reporter -->
                @elseif(isset($locationData) && $locationData['type'] == 'district')
                    <div>
                        <label class="form-label">राज्य</label>
                        <input type="text" class="form-input" value="{{ $locationData['district']->state->name ?? 'Your Assigned State' }}" disabled>
                        <input type="hidden" name="state_id" value="{{ $locationData['district']->state_id ?? '' }}">
                    </div>

                    <div>
                        <label class="form-label">जिला <span class="text-red-500">*</span></label>
                        <input type="text" class="form-input" value="{{ $locationData['district']->name ?? 'Your Assigned District' }}" disabled>
                        <input type="hidden" name="district_id" value="{{ $locationData['district_id'] ?? '' }}">
                        <small class="text-gray-500">You can only post in your assigned district.</small>
                    </div>

                    <div>
                        <label class="form-label">तहसील/ब्लॉक</label>
                        <select name="tehsil_id" id="tehsilSelect" class="form-input">
                            <option value="">-- तहसील चुनें --</option>
                            @foreach($locationData['tehsils'] ?? [] as $tehsil)
                                <option value="{{ $tehsil->id }}">{{ $tehsil->name }}</option>
                            @endforeach
                        </select>
                    </div>

                <!-- Location - For Tehsil Reporter -->
                @elseif(isset($locationData) && $locationData['type'] == 'tehsil')
                    <div>
                        <label class="form-label">राज्य</label>
                        <input type="text" class="form-input" value="{{ $locationData['tehsil']->district->state->name ?? 'Your Assigned State' }}" disabled>
                        <input type="hidden" name="state_id" value="{{ $locationData['tehsil']->district->state_id ?? '' }}">
                    </div>

                    <div>
                        <label class="form-label">जिला</label>
                        <input type="text" class="form-input" value="{{ $locationData['tehsil']->district->name ?? 'Your Assigned District' }}" disabled>
                        <input type="hidden" name="district_id" value="{{ $locationData['tehsil']->district_id ?? '' }}">
                    </div>

                    <div>
                        <label class="form-label">तहसील <span class="text-red-500">*</span></label>
                        <input type="text" class="form-input" value="{{ $locationData['tehsil']->name ?? 'Your Assigned Tehsil' }}" disabled>
                        <input type="hidden" name="tehsil_id" value="{{ $locationData['tehsil_id'] ?? '' }}">
                        <small class="text-gray-500">You can only post in your assigned tehsil.</small>
                    </div>

                    <div>
                        <label class="form-label">ब्लॉक</label>
                        <select name="block_id" class="form-input">
                            <option value="">-- ब्लॉक चुनें --</option>
                            @foreach($locationData['blocks'] ?? [] as $block)
                                <option value="{{ $block->id }}">{{ $block->name }}</option>
                            @endforeach
                        </select>
                    </div>

                <!-- Location - For Block Reporter -->
                @elseif(isset($locationData) && $locationData['type'] == 'block')
                    <div>
                        <label class="form-label">राज्य</label>
                        <input type="text" class="form-input" value="{{ $locationData['block']->tehsil->district->state->name ?? 'Your Assigned State' }}" disabled>
                        <input type="hidden" name="state_id" value="{{ $locationData['block']->tehsil->district->state_id ?? '' }}">
                    </div>

                    <div>
                        <label class="form-label">जिला</label>
                        <input type="text" class="form-input" value="{{ $locationData['block']->tehsil->district->name ?? 'Your Assigned District' }}" disabled>
                        <input type="hidden" name="district_id" value="{{ $locationData['block']->tehsil->district_id ?? '' }}">
                    </div>

                    <div>
                        <label class="form-label">तहसील</label>
                        <input type="text" class="form-input" value="{{ $locationData['block']->tehsil->name ?? 'Your Assigned Tehsil' }}" disabled>
                        <input type="hidden" name="tehsil_id" value="{{ $locationData['block']->tehsil_id ?? '' }}">
                    </div>

                    <div>
                        <label class="form-label">ब्लॉक <span class="text-red-500">*</span></label>
                        <input type="text" class="form-input" value="{{ $locationData['block']->name ?? 'Your Assigned Block' }}" disabled>
                        <input type="hidden" name="block_id" value="{{ $locationData['block_id'] ?? '' }}">
                        <small class="text-gray-500">You can only post in your assigned block.</small>
                    </div>

                <!-- Location - For General Reporter (No specific assignment) -->
                @else
                    <div>
                        <label class="form-label">राज्य <span class="text-red-500">*</span></label>
                        <select name="state_id" id="stateSelect" required class="form-input" onchange="loadDistricts(this.value)">
                            <option value="">-- राज्य चुनें --</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">जिला <span class="text-red-500">*</span></label>
                        <select name="district_id" id="districtSelect" required class="form-input" onchange="loadTehsils(this.value)">
                            <option value="">-- पहले राज्य चुनें --</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">तहसील/ब्लॉक</label>
                        <select name="tehsil_id" id="tehsilSelect" class="form-input">
                            <option value="">-- पहले जिला चुनें --</option>
                        </select>
                    </div>
                @endif

                <!-- News Fields -->
                <div>
                    <label class="form-label">खबर का शीर्षक (Title) <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="form-input" placeholder="मुख्य हेडलाइन यहाँ लिखें..." value="{{ old('title') }}">
                </div>

                <div>
                    <label class="form-label">मुख्य फोटो अपलोड करें <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 bg-gray-50 text-center relative cursor-pointer min-h-[160px] flex flex-col items-center justify-center">
                        <input type="file" name="featured_image" required class="absolute inset-0 opacity-0 cursor-pointer w-100 h-100" onchange="previewImage(this)">
                        <div id="uploadPlaceholder">
                            <span class="text-4xl mb-2 block">📸</span>
                            <p class="text-sm font-bold text-gray-500">यहाँ क्लिक करके फोटो चुनें</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP (Max 2MB)</p>
                        </div>
                        <img id="imgPreview" class="hidden max-h-40 rounded-xl object-cover" />
                    </div>
                </div>

                <div>
                    <label class="form-label">खबर का सार (Summary) <span class="text-red-500">*</span></label>
                    <textarea name="summary" required rows="2" class="form-input placeholder-gray-400" placeholder="खबर का संक्षिप्त विवरण यहाँ लिखें...">{{ old('summary') }}</textarea>
                </div>

                <div>
                    <label class="form-label">पूरी खबर का विवरण (Content) <span class="text-red-500">*</span></label>
                    <textarea name="body" required rows="8" class="form-input placeholder-gray-400" placeholder="पूरी खबर विस्तार से यहाँ लिखें...">{{ old('body') }}</textarea>
                </div>

                <button type="submit" class="w-full bg-brand text-white font-bold py-4 rounded-xl shadow-lg hover:bg-red-700 transition active:scale-95 text-lg">
                    🚀 खबर प्रकाशित करने के लिए भेजें
                </button>
            </form>
        </div>
    </main>

    <script>
        async function loadDistricts(stateId) {
            const districtSelect = document.getElementById('districtSelect');
            const tehsilSelect = document.getElementById('tehsilSelect');
            
            if (!districtSelect) return;
            
            districtSelect.innerHTML = '<option value="">लोड हो रहा है...</option>';
            if (tehsilSelect) tehsilSelect.innerHTML = '<option value="">-- पहले जिला चुनें --</option>';

            if(!stateId) {
                districtSelect.innerHTML = '<option value="">-- पहले राज्य चुनें --</option>';
                return;
            }
            try {
                const response = await fetch(`/api/get-districts/${stateId}`);
                const districts = await response.json();
                districtSelect.innerHTML = '<option value="">-- जिला चुनें --</option>';
                districts.forEach(d => {
                    districtSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                });
            } catch (error) {
                districtSelect.innerHTML = '<option value="">त्रुटि आई</option>';
            }
        }

        async function loadTehsils(districtId) {
            const tehsilSelect = document.getElementById('tehsilSelect');
            if (!tehsilSelect) return;
            
            tehsilSelect.innerHTML = '<option value="">लोड हो रहा है...</option>';

            if(!districtId) {
                tehsilSelect.innerHTML = '<option value="">-- पहले जिला चुनें --</option>';
                return;
            }
            try {
                const response = await fetch(`/api/get-tehsils/${districtId}`);
                const tehsils = await response.json();
                tehsilSelect.innerHTML = '<option value="">-- तहसील/ब्लॉक चुनें --</option>';
                tehsils.forEach(t => {
                    tehsilSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`;
                });
            } catch (error) {
                tehsilSelect.innerHTML = '<option value="">त्रुटि आई</option>';
            }
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imgPreview').src = e.target.result;
                    document.getElementById('imgPreview').classList.remove('hidden');
                    document.getElementById('uploadPlaceholder').classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>