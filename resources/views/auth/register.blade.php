<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>रजिस्टर - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background: linear-gradient(135deg, #1e1b4b, #4c0519, #111827); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .register-card { background: white; border-radius: 24px; padding: 35px 30px; max-width: 420px; width: 100%; box-shadow: 0 25px 60px -15px rgba(0,0,0,0.5); }
        .bg-brand { background-color: #c62828; }
        .text-brand { color: #c62828; }
        .btn-primary { background: #c62828; color: white; padding: 12px 24px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: all 0.3s; width: 100%; }
        .btn-primary:hover { background: #8e0000; transform: translateY(-2px); }
        .btn-otp { background: #16a34a; color: white; padding: 12px 24px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: all 0.3s; width: 100%; }
        .btn-otp:hover { background: #15803d; transform: translateY(-2px); }
        .input-field { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 16px; transition: all 0.3s; background: #f9fafb; }
        .input-field:focus { border-color: #c62828; outline: none; box-shadow: 0 0 0 4px rgba(198,40,40,0.1); background: white; }
        .divider { display: flex; align-items: center; text-align: center; margin: 20px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #e5e7eb; }
        .divider span { padding: 0 15px; color: #9ca3af; font-size: 14px; }
    </style>
</head>
<body>

<div class="register-card">
    <!-- Logo -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-brand rounded-2xl mb-3 shadow-lg">
            <i class="fas fa-newspaper text-white text-3xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">द पब्लिक एक्सप्रेस</h1>
        <p class="text-sm text-gray-500">नया अकाउंट बनाएं</p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Email Register Form -->
    <form method="POST" action="{{ route('register.post') }}">
        @csrf
        <div class="mb-3">
            <label class="block text-sm font-bold text-gray-700 mb-1">👤 पूरा नाम</label>
            <input type="text" name="name" class="input-field" placeholder="आपका नाम" required>
        </div>
        <div class="mb-3">
            <label class="block text-sm font-bold text-gray-700 mb-1">📧 ईमेल</label>
            <input type="email" name="email" class="input-field" placeholder="your@email.com" required>
        </div>
        <div class="mb-3">
            <label class="block text-sm font-bold text-gray-700 mb-1">🔑 पासवर्ड</label>
            <input type="password" name="password" class="input-field" placeholder="••••••••" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-1">🔑 पासवर्ड कन्फर्म करें</label>
            <input type="password" name="password_confirmation" class="input-field" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-primary">
            <i class="fas fa-user-plus mr-2"></i> रजिस्टर करें
        </button>
    </form>

    <!-- Divider -->
    <div class="divider">
        <span>या</span>
    </div>

    <!-- OTP Register Button -->
    <a href="{{ route('otp.login') }}" class="btn-otp inline-block text-center">
        <i class="fas fa-mobile-alt mr-2"></i> मोबाइल नंबर से रजिस्टर करें
    </a>

    <!-- Login Link -->
    <div class="mt-4 text-center text-sm text-gray-600">
        पहले से अकाउंट है? 
        <a href="{{ route('login') }}" class="text-brand font-bold hover:underline">
            लॉगिन करें
        </a>
    </div>

    <div class="mt-3 text-center text-xs text-gray-400">
        <i class="fas fa-shield-alt mr-1"></i> 100% सुरक्षित
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>