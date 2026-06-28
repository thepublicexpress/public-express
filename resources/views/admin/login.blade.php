<!DOCTYPE html>
<html lang="hi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Admin Login | द पब्लिक एक्सप्रेस</title>
<!-- Google Fonts & Font Awesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:"Noto Sans Devanagari",sans-serif;
    /* द पब्लिक एक्सप्रेस ब्रांड थीम के अनुसार गहरा मैरून और स्लेट ग्रे बैकग्राउंड ग्रेडिएंट */
    background: linear-gradient(135deg, #1e1b4b, #4c0519, #111827);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 20px;
}
.login-card{
    background:#ffffff;
    border-radius:24px;
    padding:35px 30px;
    width:100%;
    max-width:420px;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.login-brand{text-align:center;margin-bottom:28px}
.login-brand .logo-container{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
    border-radius: 18px;
    margin-bottom: 14px;
    box-shadow: 0 10px 20px rgba(185, 28, 28, 0.3);
}
.login-brand .logo-container i{
    font-size: 34px;
    color: #ffffff;
}
.login-brand h1{font-size:22px;font-weight:700;color:#0f172a;margin-bottom:4px}
.login-brand .tagline{color:#dc2626;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:2px}
.login-brand p.sub-text{color:#64748b;font-size:13px;font-weight:500}

.form-group{margin-bottom:18px}
.form-group label{display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px}

/* इनपुट फील्ड्स आइकॉन के साथ */
.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.input-wrapper .input-icon {
    position: absolute;
    left: 14px;
    color: #94a3b8;
    font-size: 14px;
}
.form-control{
    width:100%;
    padding:12px 16px 12px 40px;
    border:2px solid #e2e8f0;
    border-radius:12px;
    font-size:14px;
    font-family:inherit;
    color: #0f172a;
    transition: all 0.2s ease;
}
.form-control:focus{
    outline:none;
    border-color:#b91c1c; /* फोकस होने पर ब्रांड मैरून बॉर्डर */
    box-shadow:0 0 0 4px rgba(185, 28, 28, 0.1);
}

/* पासवर्ड आँख वाला टॉगल बटन */
.password-toggle {
    position: absolute;
    right: 14px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 14px;
}
.password-toggle:hover { color: #64748b; }

.btn-login{
    width:100%;
    padding:13px;
    background: linear-gradient(135deg, #b91c1c, #991b1b); /* ब्रांड कलर */
    color:#fff;
    border:none;
    border-radius:12px;
    font-size:15px;
    font-weight:700;
    cursor:pointer;
    font-family:inherit;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.btn-login:hover{
    background: linear-gradient(135deg, #991b1b, #7f1d1d);
    transform:translateY(-1px);
    box-shadow:0 8px 20px rgba(153, 27, 27, 0.3);
}
.btn-login:active { transform: translateY(0); }

.error-box{
    background:#fef2f2;
    border-left:4px solid #dc2626;
    color:#991b1b;
    padding:12px 14px;
    border-radius:8px;
    font-size:13px;
    margin-bottom:18px;
    font-weight: 500;
    line-height: 1.5;
}

/* मोबाइल के लिए बारीक एडजस्टमेंट */
@media (max-width: 400px) {
    .login-card { padding: 25px 20px; border-radius: 20px; }
    .login-brand h1 { font-size: 20px; }
}
</style>
</head>
<body>

<div class="login-card">
    <div class="login-brand">
        <!-- अखबार का आधुनिक आइकॉन -->
        <div class="logo-container">
            <i class="fa-solid fa-newspaper"></i>
        </div>
        <h1>द पब्लिक एक्सप्रेस</h1>
        <div class="tagline">पब्लिक की आवाज़</div>
        <p class="sub-text">एडमिन कंट्रोल पैनल लॉगिन</p>
    </div>

    <!-- त्रुटि संदेश (लारावेल स्टाइल में) -->
    @if ($errors->any())
        <div class="error-box">
            <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px; color: #dc2626;"></i>
            <span>{!! implode('<br>', $errors->all()) !!}</span>
        </div>
    @endif

    <!-- एक्शन रूट आपके ओरिजिनल फॉर्म के अनुसार परफेक्ट सेट है -->
    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        
        <!-- ईमेल एड्रेस फील्ड -->
        <div class="form-group">
            <label for="email">ईमेल आईडी (Email)</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-envelope input-icon"></i>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@thepublicexpress.com">
            </div>
        </div>
        
        <!-- पासवर्ड फील्ड (टॉगल के साथ) -->
        <div class="form-group">
            <label for="password">पासवर्ड (Password)</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock input-icon"></i>
                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <i class="fa-solid fa-eye" id="eye-icon"></i>
                </button>
            </div>
        </div>
        
        <!-- सबमिट बटन -->
        <button type="submit" class="btn-login">
            <span>लॉगिन करें</span> 
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </button>
    </form>
</div>

<!-- पासवर्ड हाइड/शो करने का जावास्क्रिप्ट -->
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>