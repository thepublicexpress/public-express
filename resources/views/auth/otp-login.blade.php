@extends('layouts.app')

@section('title', 'OTP Login - द पब्लिक एक्सप्रेस')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-lg border border-slate-200 my-10">
    <h2 class="text-2xl font-black text-slate-900 text-center mb-6">📱 OTP से लॉगिन करें</h2>
    <p class="text-sm text-slate-500 text-center mb-6">अपने मोबाइल नंबर पर भेजे गए OTP के माध्यम से लॉगिन करें</p>

    <!-- Step 1: Mobile Number Input -->
    <div id="step1">
        <div class="mb-4">
            <label class="block text-sm font-bold text-slate-700 mb-2">मोबाइल नंबर <span class="text-red-500">*</span></label>
            <input type="text" id="loginMobile" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-brand" placeholder="9876543210" maxlength="10" required>
        </div>
        <button id="sendOtpBtn" class="w-full bg-brand text-white font-bold py-2 rounded-lg hover:bg-red-700 transition shadow-md">
            OTP भेजें
        </button>
        <p id="loginMessage" class="text-sm text-center mt-3"></p>
        <p class="text-center text-sm mt-4">
            <a href="{{ route('otp.register') }}" class="text-brand hover:underline">नया अकाउंट बनाएं</a>
        </p>
    </div>

    <!-- Step 2: OTP Verification -->
    <div id="step2" style="display: none;">
        <div class="mb-4">
            <label class="block text-sm font-bold text-slate-700 mb-2">OTP दर्ज करें <span class="text-red-500">*</span></label>
            <input type="text" id="loginOtp" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-brand" placeholder="123456" maxlength="6" required>
            <small class="text-slate-400 text-xs">आपके मोबाइल और ईमेल पर भेजा गया OTP दर्ज करें</small>
        </div>
        <button id="verifyOtpBtn" class="w-full bg-green-600 text-white font-bold py-2 rounded-lg hover:bg-green-700 transition shadow-md">
            लॉगिन करें
        </button>
        <button id="resendOtpBtn" class="w-full mt-2 text-brand font-bold text-sm hover:underline">
            OTP पुनः भेजें
        </button>
        <p id="loginVerifyMessage" class="text-sm text-center mt-3"></p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        const verifyOtpBtn = document.getElementById('verifyOtpBtn');
        const resendOtpBtn = document.getElementById('resendOtpBtn');
        const loginMobile = document.getElementById('loginMobile');
        const loginOtp = document.getElementById('loginOtp');
        const loginMessage = document.getElementById('loginMessage');
        const loginVerifyMessage = document.getElementById('loginVerifyMessage');

        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Send OTP
        sendOtpBtn.addEventListener('click', function() {
            const mobile = loginMobile.value.trim();
            if (!mobile || mobile.length !== 10) {
                loginMessage.innerHTML = '<span class="text-red-500">कृपया सही 10-अंकीय मोबाइल नंबर दर्ज करें।</span>';
                return;
            }

            loginMessage.innerHTML = '<span class="text-blue-500">OTP भेजा जा रहा है...</span>';
            sendOtpBtn.disabled = true;

            fetch('{{ route("otp.generate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    mobile: mobile,
                    type: 'login'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loginMessage.innerHTML = '<span class="text-green-500">✅ OTP सफलतापूर्वक भेज दिया गया! कृपया अपना OTP दर्ज करें।</span>';
                    // ✅ OTP को Screen पर नहीं दिखा रहे
                    // data.otp का उपयोग न करें
                    step1.style.display = 'none';
                    step2.style.display = 'block';
                } else {
                    loginMessage.innerHTML = '<span class="text-red-500">❌ ' + (data.message || 'OTP भेजने में त्रुटि हुई।') + '</span>';
                }
            })
            .catch(err => {
                loginMessage.innerHTML = '<span class="text-red-500">❌ नेटवर्क त्रुटि। कृपया पुनः प्रयास करें।</span>';
            })
            .finally(() => {
                sendOtpBtn.disabled = false;
            });
        });

        // Verify OTP
        verifyOtpBtn.addEventListener('click', function() {
            const mobile = loginMobile.value.trim();
            const otp = loginOtp.value.trim();

            if (!otp || otp.length !== 6) {
                loginVerifyMessage.innerHTML = '<span class="text-red-500">कृपया 6-अंकीय OTP दर्ज करें।</span>';
                return;
            }

            loginVerifyMessage.innerHTML = '<span class="text-blue-500">सत्यापन हो रहा है...</span>';
            verifyOtpBtn.disabled = true;

            fetch('{{ route("otp.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    mobile: mobile,
                    otp: otp,
                    type: 'login'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loginVerifyMessage.innerHTML = '<span class="text-green-500">✅ लॉगिन सफल! Redirection हो रहा है...</span>';
                    window.location.href = data.redirect || '/';
                } else {
                    loginVerifyMessage.innerHTML = '<span class="text-red-500">❌ ' + (data.message || 'गलत OTP या OTP समाप्त हो गया।') + '</span>';
                }
            })
            .catch(err => {
                loginVerifyMessage.innerHTML = '<span class="text-red-500">❌ नेटवर्क त्रुटि। कृपया पुनः प्रयास करें।</span>';
            })
            .finally(() => {
                verifyOtpBtn.disabled = false;
            });
        });

        // Resend OTP
        resendOtpBtn.addEventListener('click', function() {
            const mobile = loginMobile.value.trim();

            if (!mobile || mobile.length !== 10) {
                loginVerifyMessage.innerHTML = '<span class="text-red-500">कृपया सही मोबाइल नंबर दर्ज करें।</span>';
                return;
            }

            loginVerifyMessage.innerHTML = '<span class="text-blue-500">OTP पुनः भेजा जा रहा है...</span>';
            resendOtpBtn.disabled = true;

            fetch('{{ route("otp.resend") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    mobile: mobile
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loginVerifyMessage.innerHTML = '<span class="text-green-500">✅ ' + data.message + '</span>';
                } else {
                    loginVerifyMessage.innerHTML = '<span class="text-red-500">❌ ' + (data.message || 'OTP पुनः भेजने में त्रुटि।') + '</span>';
                }
            })
            .catch(err => {
                loginVerifyMessage.innerHTML = '<span class="text-red-500">❌ नेटवर्क त्रुटि। कृपया पुनः प्रयास करें।</span>';
            })
            .finally(() => {
                resendOtpBtn.disabled = false;
            });
        });
    });
</script>
@endsection