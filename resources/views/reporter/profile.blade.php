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
    </style>
</head>
<body>

    <header class="bg-brand text-white p-4 sticky top-0 z-50 flex items-center gap-3 shadow-md">
        <a href="{{ route('reporter.dashboard') }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="text-lg font-bold">मेरी प्रोफाइल</h1>
    </header>

    <main class="p-4 max-w-lg mx-auto">
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded-xl mb-4 text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm p-5">
            <form action="{{ route('reporter.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Avatar -->
                <div class="flex flex-col items-center mb-5">
                    <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden mb-2">
                        @if($user->avatar)
                            <img src="{{ asset('storage/'.$user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl text-gray-400">📷</span>
                        @endif
                    </div>
                    <input type="file" name="avatar" accept="image/*" class="text-sm">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">पूरा नाम *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">ईमेल</label>
                    <input type="email" value="{{ $user->email }}" readonly disabled 
                        class="w-full p-3 bg-gray-100 border rounded-xl">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">मोबाइल</label>
                    <input type="text" value="{{ $user->phone }}" readonly disabled 
                        class="w-full p-3 bg-gray-100 border rounded-xl">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">जिला</label>
                    <select name="district_id" id="district" onchange="loadTehsils(this.value)" 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                        <option value="">जिला चुनें</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ old('district_id', $user->district_id) == $d->id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">तहसील</label>
                    <select name="tehsil_id" id="tehsil" 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                        <option value="">तहसील चुनें</option>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-bold mb-1">UPI ID (विड्रॉल के लिए)</label>
                    <input type="text" name="upi_id" value="{{ old('upi_id', $user->upi_id) }}" placeholder="yourname@upi"
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <button type="submit" class="w-full bg-brand text-white font-bold py-3 rounded-xl shadow-lg active:scale-95 transition">
                    प्रोफाइल सेव करें
                </button>
            </form>
        </div>

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
                    <p class="text-green-600 font-bold">
                        @if($user->is_verified_reporter)
                            ✅ वेरिफाइड
                        @else
                            ⏳ पेंडिंग
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </main>

    <script>
        async function loadTehsils(districtId) {
            if(!districtId) {
                document.getElementById('tehsil').innerHTML = '<option value="">तहसील चुनें</option>';
                return;
            }
            const select = document.getElementById('tehsil');
            select.innerHTML = '<option>लोड हो रहा...</option>';
            try {
                const res = await fetch(`/api/tehsils/${districtId}`);
                const data = await res.json();
                select.innerHTML = '<option value="">तहसील चुनें</option>';
                data.forEach(t => {
                    const selected = {{ old('tehsil_id', $user->tehsil_id ?? 'null') }} == t.id ? 'selected' : '';
                    select.innerHTML += `<option value="${t.id}" ${selected}>${t.name}</option>`;
                });
            } catch(e) {
                select.innerHTML = '<option value="">Error loading tehsils</option>';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const districtSelect = document.getElementById('district');
            if(districtSelect.value) {
                loadTehsils(districtSelect.value);
            }
        });
    </script>

</body>
</html>