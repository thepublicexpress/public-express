<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google-Style Sign In - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background-color: #f8f9fa; }
        .bg-brand { background-color: #c62828; }
        .text-brand { color: #c62828; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border-t-8 border-brand">
        <div class="p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-brand italic">द पब्लिक एक्सप्रेस</h1>
                <p class="text-gray-500 mt-2 font-bold">Google-Style Sign In</p>
            </div>

            @if($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <p class="mb-4 text-sm text-gray-600">
                यह Google-style विकल्प है। अपनी Gmail ID डालें और हम आपके लिए तुरंत एक रिपोर्टर अकाउंट बना देंगे।
            </p>

            <form method="POST" action="{{ route('reporter.google.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-2 block font-bold text-gray-700">Email / Gmail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-semibold outline-none focus:ring-2 focus:ring-red-200" placeholder="yourname@gmail.com">
                </div>
                <button type="submit" class="w-full rounded-xl bg-brand py-4 font-bold text-white shadow-lg transition-all hover:bg-red-700 active:scale-95">
                    Continue with Google
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-gray-500">
                <a href="{{ route('reporter.register') }}" class="font-semibold text-brand">रजिस्टर करें</a> या <a href="{{ route('reporter.login') }}" class="font-semibold text-brand">लॉगिन करें</a>
            </p>
        </div>

        <div class="border-t border-gray-100 bg-gray-50 p-4 text-center">
            <p class="text-xs text-gray-400">© {{ date('Y') }} The Public Express | रिपोर्टर नेटवर्क</p>
        </div>
    </div>
</body>
</html>
