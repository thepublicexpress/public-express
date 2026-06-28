<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OTP Login - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background: linear-gradient(135deg, #1e1b4b, #4c0519, #111827); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .otp-card { background: white; border-radius: 24px; padding: 35px 30px; max-width: 420px; width: 100%; box-shadow: 0 25px 60px -15px rgba(0,0,0,0.5); }
        .bg-brand { background-color: #c62828; }
        .text-brand { color: #c62828; }
        .btn-primary { background: #c62828; color: white; padding: 12px 24px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: all 0.3s; width: 100%; }
        .btn-primary:hover { background: #8e0000; transform: translateY(-2px); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .otp-input { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 16px; transition: all 0.3s; background: #f9fafb; }
        .otp-input:focus { border-color: #c62828; outline: none; box-shadow: 0 0 0 4px rgba(198,40,40,0.1); background: white; }
        .otp-digit { width: 50px; height: 60px; text-align: center; font-size: 24px; font-weight: 700; border: 2px solid #e2e8f0; border-radius: 12px; margin: 0 4px; transition: all 0.3s; background: #f9fafb; }
        .otp-digit:focus { border-color: #c62828; outline: none; box-shadow: 0 0 0 4px rgba(198,40,40,0.1); background: white; }
        .timer { color: #c62828; font-weight: 700; }
        .step { display: none; }
        .step.active { display: block; }
        .alert-message { padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; font-size: 14px; display: none; }
        .alert-success { background: #dcfce7; border: 1px solid #86efac; color: #166534; display: block; }
        .alert-danger { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; display: block; }
        .alert-warning { background: #fef3c7; border: 1px solid #fcd34d; color: #92400e; display: block; }
        .alert-info { background: #dbeafe; border: 1px solid #93c5fd; color: #1e40af; display: block; }
    </style>
</head>
<body>

<div class="otp-card">
    <!-- Logo -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-brand rounded-2xl mb-3 shadow-lg">
            <i class="fas fa-newspaper text-white text-3xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">द पब्लिक एक्सप्रेस</h1>
        <p class="text-sm text-gray-500">📱 OTP लॉगिन / रजिस्टर</p>
    </div>

    <!-- Alert Message -->
    <div id="alertMessage" class="alert-message"></div>

    <!-- Step 1: Mobile Number -->
    <div id="step1" class="step active">
        <p class="text-sm text-gray-600 mb-4">अपना मोबाइल नंबर डालें। OTP आपके मोबाइल पर भेजा जाएगा।</p>
        
        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-1">📱 मोबाइल नंबर <span class="text-red-500">*</span></label>
            <div class="flex">
                <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-xl text-gray-600 text-sm font-bold">+91</span>
                <input type="tel" id="mobileInput" maxlength="10" class="otp-input rounded-l-none" placeholder="9876543210" autofocus oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            </div>
            <p class="text-xs text-gray-400 mt-1">10 अंकों का मोबाइल नंबर</p>
        </div>

        <button id="sendOtpBtn" class="btn-primary" onclick="sendOTP()">
            <i class="fas fa-paper-plane mr-2"></i> OTP भेजें
        </button>

        <p class="text-center text-xs text-gray-400 mt-4">
            <i class="fas fa-lock mr-1"></i> आपका नंबर सुरक्षित है
        </p>
    </div>

    <!-- Step 2: OTP Verification -->
    <div id="step2" class="step">
        <p class="text-sm text-gray-600 mb-4">OTP भेजा गया है <span id="mobileDisplay" class="font-bold text-brand"></span> पर</p>
        
        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-2">🔑 OTP दर्ज करें <span class="text-red-500">*</span></label>
            <div class="flex justify-center gap-2" id="otpContainer">
                <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="otpInput(this)" data-index="0">
                <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="otpInput(this)" data-index="1">
                <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="otpInput(this)" data-index="2">
                <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="otpInput(this)" data-index="3">
                <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="otpInput(this)" data-index="4">
                <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="otpInput(this)" data-index="5">
            </div>
            <input type="hidden" id="otpComplete">
        </div>

        <div class="flex justify-between items-center mb-4">
            <span class="text-sm text-gray-500">OTP वैध: <span id="otpTimer" class="timer">10:00</span></span>
            <button id="resendBtn" class="text-brand font-bold text-sm hover:underline disabled:opacity-50" onclick="resendOTP()" disabled>
                <i class="fas fa-redo mr-1"></i> पुनः भेजें
            </button>
        </div>

        <button id="verifyOtpBtn" class="btn-primary" onclick="verifyOTP()">
            <i class="fas fa-check mr-2"></i> OTP वेरिफाई करें
        </button>

        <p class="text-center mt-4">
            <button onclick="goToStep(1)" class="text-sm text-gray-500 hover:text-brand transition">← नंबर बदलें</button>
        </p>
    </div>

    <!-- Step 3: Registration Form -->
    <div id="step3" class="step">
        <div class="bg-green-50 border border-green-200 rounded-xl p-3 mb-4">
            <p class="text-sm text-green-700">✅ OTP वेरिफाई हो गया! अब अपना प्रोफाइल पूरा करें।</p>
        </div>
        
        <form id="registerForm" action="{{ route('otp.register') }}" method="POST">
            @csrf
            <input type="hidden" id="regMobile" name="mobile">

            <div class="mb-3">
                <label class="block text-sm font-bold text-gray-700 mb-1">👤 पूरा नाम <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="regName" class="otp-input" placeholder="आपका नाम" required>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-bold text-gray-700 mb-1">📧 ईमेल (वैकल्पिक)</label>
                <input type="email" name="email" id="regEmail" class="otp-input" placeholder="your@email.com">
                <p class="text-xs text-gray-400 mt-1">ईमेल वैकल्पिक है, लेकिन इससे आपको अपडेट मिलेंगे</p>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-bold text-gray-700 mb-1">📍 आपका राज्य</label>
                <select name="state_id" id="regState" class="otp-input">
                    <option value="">-- राज्य चुनें --</option>
                    @foreach(\App\Models\State::where('is_active', true)->get() as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-bold text-gray-700 mb-1">📍 आपका जिला</label>
                <select name="district_id" id="regDistrict" class="otp-input">
                    <option value="">-- जिला चुनें --</option>
                </select>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-user-plus mr-2"></i> अकाउंट बनाएं
            </button>

            <p class="text-center text-xs text-gray-400 mt-3">
                पहले से अकाउंट है? <a href="{{ route('login') }}" class="text-brand font-bold hover:underline">लॉगिन करें</a>
            </p>
        </form>
    </div>

    <!-- Footer -->
    <div class="mt-6 pt-4 border-t border-gray-100 text-center">
        <p class="text-xs text-gray-400">
            <i class="fas fa-shield-alt mr-1"></i> 100% सुरक्षित OTP वेरिफिकेशन
        </p>
    </div>
</div>

<script>
// ===== GLOBALS =====
let timerInterval = null;
let secondsRemaining = 0;
let currentMobile = '';

// ===== STEP NAVIGATION =====
function goToStep(step) {
    document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
    document.getElementById('step' + step).classList.add('active');
}

// ===== OTP INPUT HANDLING =====
function otpInput(input) {
    if (input.value.length === 1) {
        let next = parseInt(input.dataset.index) + 1;
        if (next < 6) {
            document.querySelector(`.otp-digit[data-index="${next}"]`).focus();
        }
    }
    
    let otp = '';
    document.querySelectorAll('.otp-digit').forEach(el => otp += el.value);
    document.getElementById('otpComplete').value = otp;
    
    if (otp.length === 6) {
        setTimeout(verifyOTP, 500);
    }
}

document.querySelectorAll('.otp-digit').forEach((el, index) => {
    el.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && this.value === '' && index > 0) {
            document.querySelector(`.otp-digit[data-index="${index-1}"]`).focus();
        }
    });
    el.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});

// ===== SEND OTP =====
function sendOTP() {
    const mobile = document.getElementById('mobileInput').value.trim();
    
    if (mobile.length !== 10) {
        showAlert('warning', '⚠️ कृपया 10 अंकों का सही मोबाइल नंबर डालें।');
        return;
    }
    
    const btn = document.getElementById('sendOtpBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> भेज रहे हैं...';
    
    fetch('{{ route("otp.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ mobile: mobile })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> OTP भेजें';
        
        if (data.success) {
            currentMobile = data.mobile;
            document.getElementById('mobileDisplay').textContent = '+91-' + data.mobile;
            document.getElementById('regMobile').value = data.mobile;
            
            if (data.otp) {
                showAlert('info', '📱 आपका OTP है: ' + data.otp);
            } else {
                showAlert('success', '✅ OTP भेज दिया गया है!');
            }
            
            goToStep(2);
            startTimer(600);
            
            setTimeout(() => {
                document.getElementById('resendBtn').disabled = false;
            }, 60000);
        } else {
            if (data.registered) {
                showAlert('info', 'ℹ️ यह नंबर पहले से रजिस्टर है। कृपया लॉगिन करें।');
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 2000);
            } else {
                showAlert('danger', '❌ ' + data.message);
            }
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> OTP भेजें';
        showAlert('danger', '❌ कुछ गलत हो गया। कृपया पुनः प्रयास करें।');
    });
}

// ===== VERIFY OTP =====
function verifyOTP() {
    const otp = document.getElementById('otpComplete').value;
    
    if (otp.length !== 6) {
        showAlert('warning', '⚠️ कृपया 6 अंकों का OTP दर्ज करें।');
        return;
    }
    
    const btn = document.getElementById('verifyOtpBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> वेरिफाई कर रहे हैं...';
    
    fetch('{{ route("otp.verify") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            mobile: currentMobile, 
            otp: otp 
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i> OTP वेरिफाई करें';
        
        if (data.success) {
            if (data.exists) {
                showAlert('success', '✅ OTP वेरिफाई हो गया! आपको डैशबोर्ड पर भेजा जा रहा है...');
                setTimeout(() => {
                    window.location.href = '/';
                }, 1500);
            } else {
                showAlert('success', '✅ OTP वेरिफाई हो गया! अब अपना प्रोफाइल पूरा करें।');
                goToStep(3);
                stopTimer();
            }
        } else {
            showAlert('danger', '❌ ' + data.message);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i> OTP वेरिफाई करें';
        showAlert('danger', '❌ कुछ गलत हो गया। कृपया पुनः प्रयास करें।');
    });
}

// ===== RESEND OTP =====
function resendOTP() {
    const btn = document.getElementById('resendBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> भेज रहे...';
    
    fetch('{{ route("otp.resend") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ mobile: currentMobile })
    })
    .then(res => res.json())
    .then(data => {
        btn.innerHTML = '<i class="fas fa-redo mr-1"></i> पुनः भेजें';
        if (data.success) {
            showAlert('success', '✅ नया OTP भेज दिया गया है!');
            document.querySelectorAll('.otp-digit').forEach(el => el.value = '');
            document.getElementById('otpComplete').value = '';
            document.querySelector('.otp-digit[data-index="0"]').focus();
            startTimer(600);
            setTimeout(() => { btn.disabled = false; }, 60000);
        } else {
            showAlert('danger', '❌ ' + data.message);
            btn.disabled = false;
        }
    })
    .catch(err => {
        btn.innerHTML = '<i class="fas fa-redo mr-1"></i> पुनः भेजें';
        btn.disabled = false;
        showAlert('danger', '❌ कुछ गलत हो गया।');
    });
}

// ===== TIMER =====
function startTimer(seconds) {
    secondsRemaining = seconds;
    updateTimerDisplay();
    stopTimer();
    
    timerInterval = setInterval(() => {
        secondsRemaining--;
        updateTimerDisplay();
        if (secondsRemaining <= 0) {
            stopTimer();
            document.getElementById('resendBtn').disabled = false;
        }
    }, 1000);
}

function stopTimer() {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
}

function updateTimerDisplay() {
    const mins = Math.floor(secondsRemaining / 60);
    const secs = secondsRemaining % 60;
    document.getElementById('otpTimer').textContent = 
        String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
}

// ===== ALERT SYSTEM =====
function showAlert(type, message) {
    const alertDiv = document.getElementById('alertMessage');
    alertDiv.className = 'alert-message alert-' + type;
    alertDiv.textContent = message;
    alertDiv.style.display = 'block';
}

// ===== STATE → DISTRICT LOADING =====
document.getElementById('regState').addEventListener('change', function() {
    const stateId = this.value;
    const districtSelect = document.getElementById('regDistrict');
    
    districtSelect.innerHTML = '<option value="">-- जिला चुनें --</option>';
    if (!stateId) return;
    
    fetch('/api/get-districts/' + stateId)
        .then(r => r.json())
        .then(data => {
            data.forEach(d => {
                districtSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
            });
        });
});

// ===== ENTER KEY SUPPORT =====
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const activeStep = document.querySelector('.step.active');
        if (activeStep) {
            const id = activeStep.id;
            if (id === 'step1') sendOTP();
            else if (id === 'step2') verifyOTP();
        }
    }
});
</script>
</body>
</html>