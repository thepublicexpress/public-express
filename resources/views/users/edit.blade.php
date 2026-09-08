<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>रिपोर्टर प्रोफ़ाइल एडिट</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f9f9f9; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .preview-img { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; margin-bottom: 10px; border: 2px solid #ddd; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-error { color: red; font-size: 14px; margin-top: 5px; }
        img { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ रिपोर्टर प्रोफ़ाइल एडिट करें</h2>

        {{-- ✅ सफलता या त्रुटि का मैसेज दिखाना --}}
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        {{-- ✅ फॉर्म (enctype="multipart/form-data" बिल्कुल न भूलें) --}}
        <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')  {{-- PUT method के लिए जरूरी --}}

            {{-- 1. नाम --}}
            <div class="form-group">
                <label>पूरा नाम (Full Name)</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            {{-- 2. ईमेल --}}
            <div class="form-group">
                <label>ईमेल (Email)</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            {{-- 3. फोन --}}
            <div class="form-group">
                <label>फोन नंबर (Phone)</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                @error('phone') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            {{-- 4. Bio (अगर bio कॉलम है तो) --}}
            <div class="form-group">
                <label>जीवन परिचय (Bio / Description)</label>
                <textarea name="bio" rows="4">{{ old('bio', $user->bio ?? '') }}</textarea>
                @error('bio') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            {{-- 5. प्रोफाइल फोटो (Current + Upload) --}}
            <div class="form-group">
                <label>प्रोफाइल फोटो</label>
                <br>
                {{-- अगर पहले से फोटो है तो दिखाएँ --}}
                @if($user->photo && file_exists(public_path($user->photo)))
                    <img src="{{ asset($user->photo) }}" class="preview-img" alt="Current Photo">
                    <br>
                @else
                    <p style="color: gray;">कोई फोटो अपलोड नहीं है</p>
                @endif
                
                {{-- नई फोटो अपलोड करने का इनपुट --}}
                <input type="file" name="photo" accept="image/*">
                <small style="display:block; color: gray;">Allowed: jpg, png, gif (Max 2MB)</small>
                @error('photo') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            {{-- सबमिट बटन --}}
            <button type="submit" class="btn">✅ प्रोफ़ाइल अपडेट करें</button>
        </form>
    </div>
</body>
</html>