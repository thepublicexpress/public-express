<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>रिपोर्टर रजिस्टर - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background-color: #f3f4f6; }
        .bg-brand { background-color: #c62828; }
        .text-brand { color: #c62828; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-brand p-4 text-center">
            <h1 class="text-2xl font-bold text-white">द पब्लिक एक्सप्रेस</h1>
            <p class="text-white/80 text-sm">रिपोर्टर रजिस्टर</p>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('reporter.register.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-bold text-gray-700 mb-1">पूरा नाम *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" 
                        class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-bold text-gray-700 mb-1">ईमेल *</label>
                    <input type="email" name="email" required value="{{ old('email') }}" 
                        class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-bold text-gray-700 mb-1">मोबाइल नंबर *</label>
                    <input type="tel" name="phone" required value="{{ old('phone') }}" 
                        class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-bold text-gray-700 mb-1">पासवर्ड *</label>
                    <input type="password" name="password" required 
                        class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">पासवर्ड कन्फर्म करें *</label>
                    <input type="password" name="password_confirmation" required 
                        class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <button type="submit" class="w-full bg-brand text-white font-bold py-3 rounded-xl shadow-lg active:scale-95 transition text-lg">
                    रजिस्टर करें
                </button>

                <p class="text-center mt-4 text-sm text-gray-600">
                    पहले से अकाउंट है? 
                    <a href="{{ route('reporter.login') }}" class="text-brand font-bold">लॉगिन करें</a>
                </p>
            </form>
        </div>
        
        <div class="bg-gray-50 p-3 text-center border-t border-gray-100">
            <p class="text-xs text-gray-400">हाइपरलोकल न्यूज़ प्लेटफॉर्म</p>
        </div>
    </div>

</body>
</html>