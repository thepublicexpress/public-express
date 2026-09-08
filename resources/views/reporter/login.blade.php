<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>रिपोर्टर लॉगिन - द पब्लिक एक्सप्रेस</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1a1a2e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header img {
            height: 60px;
            margin-bottom: 10px;
        }
        .login-header h3 {
            font-weight: 700;
            color: #1a1a2e;
        }
        .login-header p {
            color: #6c757d;
            font-size: 14px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #c62828;
            box-shadow: 0 0 0 0.2rem rgba(198, 40, 40, 0.15);
        }
        .btn-login {
            background: #c62828;
            color: white;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            width: 100%;
        }
        .btn-login:hover {
            background: #b71c1c;
            transform: translateY(-2px);
        }
        .btn-login i {
            margin-right: 8px;
        }
        .btn-otp {
            background: #0f172a;
            color: white;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            width: 100%;
        }
        .btn-otp:hover {
            background: #1a1a2e;
            transform: translateY(-2px);
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .register-link a {
            color: #c62828;
            font-weight: 600;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 12px;
        }
        .otp-section {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        .otp-section.show {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .otp-input {
            text-align: center;
            font-size: 24px;
            letter-spacing: 10px;
            font-weight: bold;
        }
        .resend-otp {
            font-size: 13px;
            color: #c62828;
            cursor: pointer;
            text-decoration: none;
        }
        .resend-otp:hover {
            text-decoration: underline;
        }
        .timer {
            font-size: 13px;
            color: #6c757d;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
        }
        .input-group .form-control {
            border-left: none;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <img src="{{ asset('images/logo.png') }}" alt="द पब्लिक एक्सप्रेस" onerror="this.src='https://placehold.co/200x60/c62828/white?text=The+Public+Express'">
            <h3>रिपोर्टर लॉगिन</h3>
            <p>अपने अकाउंट में लॉगिन करें</p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Mobile Form -->
        <div id="step1" @if(session('otp_sent')) style="display:none;" @endif>
            <form method="POST" action="{{ route('reporter.otp.generate') }}" id="mobileLoginForm">
                @csrf
                <div class="mb-3">
                    <label for="mobile" class="form-label fw-semibold">मोबाइल नंबर</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-2 border-end-0 rounded-start-3">
                            <i class="fas fa-mobile-alt text-muted"></i>
                        </span>
                        <input type="tel" class="form-control border-start-0 rounded-end-3" 
                               id="mobile" name="mobile" placeholder="9876543210" inputmode="numeric" maxlength="15" 
                               value="{{ old('mobile', session('mobile')) }}" required autofocus>
                    </div>
                </div>
                <input type="hidden" name="type" value="login">

                <button type="submit" class="btn-otp" id="sendOtpButton">
                    <i class="fas fa-paper-plane"></i> OTP भेजें
                </button>
            </form>
        </div>

        <!-- OTP Verification -->
        <div id="step2" class="otp-section {{ session('otp_sent') ? 'show' : '' }}">
            @if(session('otp_sent') && session('email'))
                <hr>
                <p class="text-center text-muted small">
                    <i class="fas fa-envelope"></i> OTP {{ session('email') }} पर भेज दिया गया है
                </p>
                
                <form method="POST" action="{{ route('reporter.otp.verify') }}" id="otpLoginForm">
                    @csrf
                    <input type="hidden" name="mobile" value="{{ session('mobile') }}">
                    <input type="hidden" name="type" value="login">
                    
                    <div class="mb-3">
                        <label for="otp" class="form-label fw-semibold">OTP दर्ज करें</label>
                        <input type="text" class="form-control otp-input @error('otp') is-invalid @enderror" 
                               id="otp" name="otp" placeholder="------" maxlength="6" 
                               pattern="[0-9]{6}" inputmode="numeric" required>
                        @error('otp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="timer" id="timer">⏱️ 10:00</span>
                        <a href="#" class="resend-otp" id="resendOtp" onclick="resendOTP(event)">OTP पुनः भेजें</a>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> लॉगिन करें
                    </button>
                </form>
            @endif
        </div>

        <div class="register-link">
            <p>नए रिपोर्टर हैं? <a href="{{ route('reporter.register') }}">रजिस्टर करें</a></p>
            <a href="/" class="text-decoration-none" style="color: #6c757d; font-size: 13px;">
                <i class="fas fa-arrow-left"></i> होम पेज पर वापस जाएं
            </a>
        </div>
    </div>

    <script>
        // OTP Input - Auto submit when 6 digits entered
        document.getElementById('otp')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length === 6) {
                this.form.submit();
            }
        });

        // Timer for OTP expiry (10 minutes)
        @if(session('otp_sent'))
            let timeLeft = 600;
            const timerElement = document.getElementById('timer');
            
            if (timerElement) {
                const timerInterval = setInterval(() => {
                    timeLeft--;
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    timerElement.textContent = `⏱️ ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        timerElement.textContent = '⏱️ OTP समाप्त हो गया';
                        document.getElementById('resendOtp').style.display = 'block';
                    }
                }, 1000);
            }
        @endif

        // Resend OTP
        function resendOTP(event) {
            event.preventDefault();
            
            const mobile = document.querySelector('input[name="mobile"]').value;
            if (!mobile) {
                alert('कृपया पहले अपना मोबाइल नंबर दर्ज करें।');
                return;
            }

            const button = document.getElementById('resendOtp');
            button.textContent = '⏳ भेजा जा रहा...';
            button.style.pointerEvents = 'none';

            fetch('{{ route("reporter.otp.resend") }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ mobile: mobile })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ OTP पुनः भेज दिया गया है!');
                    location.reload();
                } else {
                    alert('❌ ' + (data.error || 'OTP पुनः भेजने में समस्या आई।'));
                    button.textContent = 'OTP पुनः भेजें';
                    button.style.pointerEvents = 'auto';
                }
            })
            .catch(err => {
                alert('❌ कुछ गड़बड़ हो गई। कृपया पुनः प्रयास करें।');
                button.textContent = 'OTP पुनः भेजें';
                button.style.pointerEvents = 'auto';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.focus();
            }

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const submitJsonForm = (form, button, onSuccess) => {
                form.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    button.disabled = true;
                    const payload = Object.fromEntries(new FormData(form).entries());
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                            body: new URLSearchParams(payload)
                        });
                        const data = await response.json();
                        if (data.success) onSuccess(data);
                        else alert(data.message || 'अनुरोध पूरा नहीं हो सका।');
                    } catch (error) {
                        alert('सर्वर से कनेक्शन नहीं हो सका। कृपया दोबारा प्रयास करें।');
                    } finally {
                        button.disabled = false;
                    }
                });
            };

            const mobileForm = document.getElementById('mobileLoginForm');
            if (mobileForm) {
                submitJsonForm(mobileForm, document.getElementById('sendOtpButton'), () => window.location.reload());
            }

            const otpForm = document.getElementById('otpLoginForm');
            if (otpForm) {
                submitJsonForm(otpForm, otpForm.querySelector('button[type="submit"]'), data => window.location.href = data.redirect);
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>