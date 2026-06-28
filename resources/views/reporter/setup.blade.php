<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>प्रोफाइल सेटअप - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background: #f3f4f6; }
        .bg-brand { background-color: #c62828; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-brand p-4 text-center">
            <h1 class="text-2xl font-bold text-white">द पब्लिक एक्सप्रेस</h1>
            <p class="text-white/80 text-sm">रिपोर्टर प्रोफाइल सेटअप</p>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('reporter.setup.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">पूरा नाम *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">जिला *</label>
                    <select name="district_id" id="district" required onchange="loadTehsils(this.value)" 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                        <option value="">जिला चुनें</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ old('district_id', $user->district_id ?? '') == $d->id ? 'selected' : '' }}>
                                {{ $d->name }} ({{ $d->name_hi ?? $d->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">तहसील</label>
                    <select name="tehsil_id" id="tehsil" 
                        class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                        <option value="">पहले जिला चुनें</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">प्रोफाइल फोटो</label>
                    <input type="file" name="avatar" accept="image/*" 
                        class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-red-50 file:text-brand">
                </div>

                <button type="submit" class="w-full bg-brand text-white font-bold py-3 rounded-xl shadow-lg active:scale-95 transition">
                    प्रोफाइल सेव करें →
                </button>
            </form>
        </div>
    </div>

    <script>
        async function loadTehsils(districtId) {
            if(!districtId) return;
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
        
        // Load tehsils on page load if district is already selected
        document.addEventListener('DOMContentLoaded', function() {
            const districtSelect = document.getElementById('district');
            if(districtSelect.value) {
                loadTehsils(districtSelect.value);
            }
        });
    </script>
</body>
</html>