<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>लीडरबोर्ड - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Noto Sans Devanagari', sans-serif; background-color: #f3f4f6; } .bg-brand { background-color: #c62828; } </style>
</head>
<body class="bg-gray-100">
    <header class="bg-brand text-white p-4 flex items-center gap-4">
        <a href="/"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
        <h1 class="text-xl font-bold">टॉप रिपोर्टर्स (Leaderboard)</h1>
    </header>

    <main class="p-4 max-w-lg mx-auto">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-800 p-6 text-center text-white">
                <p class="text-sm font-bold opacity-80 uppercase tracking-widest">हमारे बेहतरीन साथी</p>
                <h2 class="text-2xl font-black mt-1">द पब्लिक एक्सप्रेस स्टार्स</h2>
            </div>

            <div class="divide-y">
                @foreach($reporters as $index => $reporter)
                <div class="flex items-center p-4 gap-4 bg-white">
                    <div class="w-8 font-black text-xl text-gray-300">#{{ $index + 1 }}</div>
                    <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center overflow-hidden border-2 border-red-100">
                        @if($reporter->avatar)
                            <img src="{{ asset('storage/'.$reporter->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-brand font-black text-xl">{{ substr($reporter->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <h4 class="font-bold text-gray-800">{{ $reporter->name }}</h4>
                        <p class="text-xs text-gray-400 font-bold uppercase">{{ $reporter->district->name ?? 'उत्तर प्रदेश' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-black text-brand">{{ number_format($reporter->points) }}</p>
                        <p class="text-[8px] font-bold text-gray-400 uppercase">पॉइंट्स</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8 p-6 bg-red-50 rounded-2xl border border-red-100 text-center">
            <h3 class="font-bold text-brand mb-2">क्या आप भी रिपोर्टर बनना चाहते हैं?</h3>
            <p class="text-sm text-gray-600 mb-4">आज ही जुड़ें और अपने क्षेत्र की आवाज़ बनें। हर खबर पर पायें पॉइंट्स और इनाम।</p>
            <a href="{{ route('reporter.login') }}" class="inline-block bg-brand text-white px-6 py-2 rounded-full font-bold text-sm shadow-md">रजिस्टर करें</a>
        </div>
    </main>
</body>
</html>