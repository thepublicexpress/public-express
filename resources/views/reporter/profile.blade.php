<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>प्रोफाइल - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background-color: #f3f4f6; }
        .bg-brand { background-color: #c62828; }
        .text-brand { color: #c62828; }
        .preview-img { width: 96px; height: 96px; object-fit: cover; border-radius: 50%; border: 3px solid #c62828; }
        .form-control:focus {
            border-color: #c62828;
            box-shadow: 0 0 0 3px rgba(198, 40, 40, 0.2);
        }
    </style>
</head>
<body>

    <header class="bg-brand text-white p-4 sticky top-0 z-50 flex items-center gap-3 shadow-md">
        <a href="{{ route('reporter.dashboard') }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-lg font-bold">👤 मेरी प्रोफाइल</h1>
    </header>

    <main class="p-4 max-w-lg mx-auto pb-20">
        
        {{-- ✅ Success Message --}}
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- ✅ Error Messages --}}
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-4 text-sm">
                @foreach($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm p-5">
            
            {{-- ✅ FORM – enctype="multipart/form-data" ज़रूरी है --}}
            <form action="{{ route('reporter.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- ============================================================ --}}
                {{-- ✅ PROFILE PHOTO --}}
                {{-- ============================================================ --}}
                <div class="flex flex-col items-center mb-5">
                    <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden mb-2">
                        @if($user->photo && file_exists(public_path($user->photo)))
                            <img src="{{ asset($user->photo) }}" class="preview-img" alt="Profile Photo">
                        @else
                            <span class="text-5xl text-gray-400">📷</span>
                        @endif
                    </div>
                    
                    {{-- Photo Input --}}
                    <input type="file" name="photo" accept="image/*" class="text-sm block w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                    @error('photo') <small class="text-red-500 text-xs">{{ $message }}</small> @enderror
                    <small class="text-gray-500 text-xs mt-1">Max: 2MB (jpg, png, gif) – नई फोटो डालें तो पुरानी हट जाएगी</small>
                </div>

                {{-- ============================================================ --}}
                {{-- ✅ BIO (जीवन परिचय) --}}
                {{-- ============================================================ --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">📝 जीवन परिचय (Bio)</label>
                    <textarea name="bio" rows="4" class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none form-control" placeholder="अपने बारे में कुछ शब्द...">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio') <small class="text-red-500 text-xs">{{ $message }}</small> @enderror
                </div>

                {{-- ============================================================ --}}
                {{-- ✅ NAME (Required) --}}
                {{-- ============================================================ --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">पूरा नाम <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none form-control">
                    @error('name') <small class="text-red-500 text-xs">{{ $message }}</small> @enderror
                </div>

                {{-- ============================================================ --}}
                {{-- ✅ EMAIL (अब Editable – Required नहीं) --}}
                {{-- ============================================================ --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">📧 ईमेल</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none form-control" 
                        placeholder="ईमेल (वैकल्पिक)">
                    <small class="text-gray-500 text-xs">ईमेल बदलना चाहते हैं? तो ही भरें – खाली छोड़ें तो पुराना रहेगा</small>
                    @error('email') <small class="text-red-500 text-xs">{{ $message }}</small> @enderror
                </div>

                {{-- ============================================================ --}}
                {{-- ✅ PHONE (अब Editable – Required नहीं) --}}
                {{-- ============================================================ --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">📱 मोबाइल</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none form-control" 
                        placeholder="मोबाइल (वैकल्पिक)">
                    <small class="text-gray-500 text-xs">मोबाइल बदलना चाहते हैं? तो ही भरें – खाली छोड़ें तो पुराना रहेगा</small>
                    @error('phone') <small class="text-red-500 text-xs">{{ $message }}</small> @enderror
                </div>

                {{-- ============================================================ --}}
                {{-- ✅ STATE / DISTRICT / TEHSIL / BLOCK (Location) --}}
                {{-- ============================================================ --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">📍 राज्य</label>
                    <select name="state_id" id="state" onchange="loadDistricts(this.value)" 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none form-control">
                        <option value="">राज्य चुनें</option>
                        @foreach($states as $s)
                            <option value="{{ $s->id }}" {{ old('state_id', $user->assigned_state_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('state_id') <small class="text-red-500 text-xs">{{ $message }}</small> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">📍 जिला</label>
                    <select name="district_id" id="district" onchange="loadTehsils(this.value)" 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none form-control">
                        <option value="">जिला चुनें</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ old('district_id', $user->assigned_district_id) == $d->id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('district_id') <small class="text-red-500 text-xs">{{ $message }}</small> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">📍 तहसील</label>
                    <select name="tehsil_id" id="tehsil" onchange="loadBlocks(this.value)" 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none form-control">
                        <option value="">तहसील चुनें</option>
                        @foreach($tehsils as $t)
                            <option value="{{ $t->id }}" {{ old('tehsil_id', $user->assigned_tehsil_id) == $t->id ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('tehsil_id') <small class="text-red-500 text-xs">{{ $message }}</small> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">📍 ब्लॉक</label>
                    <select name="block_id" id="block" 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none form-control">
                        <option value="">ब्लॉक चुनें</option>
                        @foreach($blocks as $b)
                            <option value="{{ $b->id }}" {{ old('block_id', $user->assigned_block_id) == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('block_id') <small class="text-red-500 text-xs">{{ $message }}</small> @enderror
                </div>

                {{-- ============================================================ --}}
                {{-- ✅ UPI ID (विड्रॉल के लिए) --}}
                {{-- ============================================================ --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold mb-1 text-gray-700">💳 UPI ID (विड्रॉल के लिए)</label>
                    <input type="text" name="upi_id" value="{{ old('upi_id', $user->upi_id ?? '') }}" placeholder="yourname@upi"
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none form-control">
                    @error('upi_id') <small class="text-red-500 text-xs">{{ $message }}</small> @enderror
                    <small class="text-gray-500 text-xs">UPI ID से पैसे विड्रॉ कर सकते हैं</small>
                </div>

                {{-- ============================================================ --}}
                {{-- ✅ SUBMIT BUTTON --}}
                {{-- ============================================================ --}}
                <button type="submit" class="w-full bg-brand text-white font-bold py-3 rounded-xl shadow-lg active:scale-95 transition hover:bg-red-700">
                    💾 प्रोफाइल सेव करें
                </button>
            </form>
        </div>

        {{-- ============================================================ --}}
        {{-- ✅ INFO CARD – Reporter ID, Join Date, Status --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm p-4 mt-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500">रिपोर्टर आईडी</p>
                    <p class="font-bold">#{{ $user->id }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">जॉइन दिनांक</p>
                    <p class="font-bold">{{ $user->created_at->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">स्टेटस</p>
                    <p class="font-bold {{ $user->is_verified ? 'text-green-600' : 'text-yellow-600' }}">
                        @if($user->is_verified)
                            ✅ वेरिफाइड
                        @else
                            ⏳ पेंडिंग
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </main>

    {{-- ============================================================ --}}
    {{-- ✅ JAVASCRIPT – Dynamic Location Dropdowns --}}
    {{-- ============================================================ --}}
    <script>
        // ✅ Load Districts based on State
        async function loadDistricts(stateId) {
            if(!stateId) {
                document.getElementById('district').innerHTML = '<option value="">जिला चुनें</option>';
                document.getElementById('tehsil').innerHTML = '<option value="">तहसील चुनें</option>';
                document.getElementById('block').innerHTML = '<option value="">ब्लॉक चुनें</option>';
                return;
            }
            try {
                const res = await fetch(`/api/get-districts/${stateId}`);
                const data = await res.json();
                const select = document.getElementById('district');
                select.innerHTML = '<option value="">जिला चुनें</option>';
                data.forEach(d => {
                    select.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                });
                document.getElementById('tehsil').innerHTML = '<option value="">तहसील चुनें</option>';
                document.getElementById('block').innerHTML = '<option value="">ब्लॉक चुनें</option>';
            } catch(e) {
                console.error('Error loading districts:', e);
            }
        }

        // ✅ Load Tehsils based on District
        async function loadTehsils(districtId) {
            if(!districtId) {
                document.getElementById('tehsil').innerHTML = '<option value="">तहसील चुनें</option>';
                document.getElementById('block').innerHTML = '<option value="">ब्लॉक चुनें</option>';
                return;
            }
            try {
                const res = await fetch(`/api/get-tehsils/${districtId}`);
                const data = await res.json();
                const select = document.getElementById('tehsil');
                select.innerHTML = '<option value="">तहसील चुनें</option>';
                data.forEach(t => {
                    select.innerHTML += `<option value="${t.id}">${t.name}</option>`;
                });
                document.getElementById('block').innerHTML = '<option value="">ब्लॉक चुनें</option>';
            } catch(e) {
                console.error('Error loading tehsils:', e);
            }
        }

        // ✅ Load Blocks based on Tehsil
        async function loadBlocks(tehsilId) {
            if(!tehsilId) {
                document.getElementById('block').innerHTML = '<option value="">ब्लॉक चुनें</option>';
                return;
            }
            try {
                const res = await fetch(`/api/get-blocks/${tehsilId}`);
                const data = await res.json();
                const select = document.getElementById('block');
                select.innerHTML = '<option value="">ब्लॉक चुनें</option>';
                data.forEach(b => {
                    select.innerHTML += `<option value="${b.id}">${b.name}</option>`;
                });
            } catch(e) {
                console.error('Error loading blocks:', e);
            }
        }

        // ✅ On Page Load – Pre-select Locations (if any)
        document.addEventListener('DOMContentLoaded', function() {
            const stateSelect = document.getElementById('state');
            const districtSelect = document.getElementById('district');
            
            // If state is pre-selected, load districts
            if(stateSelect.value) {
                loadDistricts(stateSelect.value);
                // Wait a bit then load tehsils if district is pre-selected
                setTimeout(() => {
                    if(districtSelect.value) {
                        loadTehsils(districtSelect.value);
                    }
                }, 300);
            }
        });
    </script>

</body>
</html>