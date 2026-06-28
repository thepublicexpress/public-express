<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>खबर एडिट करें - द पब्लिक एक्सप्रेस</title>
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
    </style>
</head>
<body class="pb-24">

    <header class="bg-brand text-white p-4 sticky top-0 z-50 shadow-md flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('reporter.news.index') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-xl font-bold">खबर संपादित करें</h1>
        </div>
    </header>

    <main class="max-w-2xl mx-auto p-4 mt-2">
        <div class="form-card">
            <form action="{{ route('reporter.news.update', $news->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="form-label">खबर का शीर्षक (Title) <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="form-input" value="{{ old('title', $news->title) }}">
                </div>

                <div>
                    <label class="form-label">कैटेगरी चुनें <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="form-input">
                        <option value="">-- चुनें --</option>
                     @foreach($categories as $category)
    <option value="{{ $category->id }}" {{ $news->category_id == $category->id ? 'selected' : '' }}>
        {{ $category->name_hindi ?? $category->name }}
    </option>
@endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">राज्य <span class="text-red-500">*</span></label>
                    <select name="state_id" id="stateSelect" required class="form-input" onchange="loadDistricts(this.value)">
                        <option value="">-- राज्य चुनें --</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ $news->state_id == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">जिला <span class="text-red-500">*</span></label>
                    <select name="district_id" id="districtSelect" required class="form-input" onchange="loadTehsils(this.value)">
                        <option value="">-- जिला चुनें --</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ $news->district_id == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">तहसील/ब्लॉक</label>
                    <select name="tehsil_id" id="tehsilSelect" class="form-input">
                        <option value="">-- तहसील/ब्लॉक चुनें --</option>
                        @foreach($tehsils as $tehsil)
                            <option value="{{ $tehsil->id }}" {{ $news->tehsil_id == $tehsil->id ? 'selected' : '' }}>{{ $tehsil->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">मुख्य फोटो बदलें (वैकल्पिक)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 bg-gray-50 text-center relative cursor-pointer min-h-[160px] flex flex-col items-center justify-center">
                        <input type="file" name="image" class="absolute inset-0 opacity-0 cursor-pointer w-100 h-100" onchange="previewImage(this)">
                        @if($news->image)
                            <div id="uploadPlaceholder" class="hidden">
                                <span class="text-4xl mb-2 block">📸</span>
                                <p class="text-sm font-bold text-gray-500">यहाँ क्लिक करके फोटो बदलें</p>
                            </div>
                            <img id="imgPreview" src="/storage/{{ $news->image }}" class="max-h-40 rounded-xl object-cover" />
                        @else
                            <div id="uploadPlaceholder">
                                <span class="text-4xl mb-2 block">📸</span>
                                <p class="text-sm font-bold text-gray-500">यहाँ क्लिक करके फोटो बदलें</p>
                            </div>
                            <img id="imgPreview" class="hidden max-h-40 rounded-xl object-cover" />
                        @endif
                    </div>
                </div>

                <div>
                    <label class="form-label">खबर का सार (Summary) <span class="text-red-500">*</span></label>
                    <textarea name="summary" required rows="2" class="form-input">{{ old('summary', $news->summary) }}</textarea>
                </div>

                <div>
                    <label class="form-label">पूरी खबर का विवरण (Content) <span class="text-red-500">*</span></label>
                    <textarea name="content" required rows="8" class="form-input">{{ old('content', $news->content) }}</textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-700 transition active:scale-95 text-lg">
                    💾 बदलाव सुरक्षित करें
                </button>
            </form>
        </div>
    </main>

    <script>
        async function loadDistricts(stateId) {
            const districtSelect = document.getElementById('districtSelect');
            const tehsilSelect = document.getElementById('tehsilSelect');
            districtSelect.innerHTML = '<option value="">लोड हो रहा है...</option>';
            tehsilSelect.innerHTML = '<option value="">-- पहले जिला चुनें --</option>';

            if(!stateId) {
                districtSelect.innerHTML = '<option value="">-- पहले राज्य चुनें --</option>';
                return;
            }
            try {
                const response = await fetch(`/api/districts/${stateId}`);
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
            tehsilSelect.innerHTML = '<option value="">लोड हो रहा है...</option>';

            if(!districtId) {
                tehsilSelect.innerHTML = '<option value="">-- पहले जिला चुनें --</option>';
                return;
            }
            try {
                const response = await fetch(`/api/tehsils/${districtId}`);
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
                    const placeholder = document.getElementById('uploadPlaceholder');
                    if(placeholder) placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>