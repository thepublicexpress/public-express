<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>समस्या रिपोर्ट करें - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 pb-10">
    <header class="bg-[#c62828] text-white p-4 sticky top-0 z-50 flex items-center gap-4">
        <a href="/"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
        <h1 class="text-lg font-bold">जन समस्या रिपोर्ट (Public Issue)</h1>
    </header>

    <main class="p-4 max-w-lg mx-auto">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 mb-6 font-bold leading-relaxed italic">"क्या आपके मोहल्ले में सड़क, बिजली या पानी की समस्या है? यहाँ फोटो के साथ रिपोर्ट करें, हम आपकी आवाज़ बनेंगे।"</p>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6 font-bold text-sm">{{ session('success') }}</div>
            @endif

            <form action="{{ route('problem.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase tracking-wider">आपका नाम*</label>
                    <input type="text" name="name" required class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:border-red-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase tracking-wider">मोबाइल नंबर*</label>
                    <input type="tel" name="phone" required class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:border-red-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase tracking-wider">जिला*</label>
                    <select name="district_id" required class="w-full p-3 bg-gray-50 border rounded-xl outline-none">
                        <option value="">जिला चुनें</option>
                        @foreach($districts as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase tracking-wider">समस्या का विषय*</label>
                    <input type="text" name="title" required placeholder="जैसे: टूटी हुई सड़क, जलभराव..." class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:border-red-500 font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase tracking-wider">समस्या का विवरण*</label>
                    <textarea name="description" rows="4" required class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:border-red-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase tracking-wider">फोटो (यदि हो)</label>
                    <input type="file" name="image" class="w-full text-sm">
                </div>

                <button type="submit" class="w-full bg-[#c62828] text-white font-bold py-4 rounded-xl shadow-lg active:scale-95 transition">समस्या दर्ज करें</button>
            </form>
        </div>
    </main>
</body>
</html>
