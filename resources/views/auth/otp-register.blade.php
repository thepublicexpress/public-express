@extends('layouts.app')

@section('title', 'OTP Registration - द पब्लिक एक्सप्रेस')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-lg border border-slate-200 my-10">
    <h2 class="text-2xl font-black text-slate-900 text-center mb-6">📝 OTP से रजिस्टर करें</h2>
    <p class="text-sm text-slate-500 text-center mb-6">अपना मोबाइल नंबर और OTP के माध्यम से नया अकाउंट बनाएं</p>

    <!-- Step 1: User Details + Mobile -->
    <div id="step1">
        <div class="mb-4">
            <label class="block text-sm font-bold text-slate-700 mb-2">पूरा नाम <span class="text-red-500">*</span></label>
            <input type="text" id="regName" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-brand" placeholder="आपका नाम" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-bold text-slate-700 mb-2">ईमेल (वैकल्पिक)</label>
            <input type="email" id="regEmail" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-brand" placeholder="email@example.com">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-bold text-slate-700 mb-2">मोबाइल नंबर <span class="text-red-500">*</span></label>
            <input type="text" id="regMobile" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-brand" placeholder="9876543210" maxlength="10" required>
        </div>
        <button id="registerSendOtpBtn" class="w-full bg-brand text-white font-bold py-2 rounded-lg hover:bg-red-700 transition shadow-md">
            OTP भेजें और रजिस्टर करें
        </button>
        <p id="regMessage" class="text-sm text-center mt-3"></p>
        <p class="text-center text-sm mt-4">
            <a href="{{ route('otp.login') }}" class="text-brand hover:underline">पहले से अकाउंट है? लॉगिन करें</a>
        </p>
    </div>

    <!-- Step 2: OTP Verification -->
    <div id="step2" style="display: none;">
        <div class="mb-4">
            <label class="block text-sm font-bold text-slate-700 mb-2">OTP दर्ज करें <span class="text-red-500">*</span></label>
            <input type="text" id="regOtp" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-brand" placeholder="123456" maxlength="6" required>
            <small class="text-slate-400 text-xs">आपके मोबाइल (और ईमेल) पर भेजा गया OTP दर्ज करें</small>
        </div>
        <button id="registerVerifyOtpBtn" class="w-full bg-green-600 text-white font-bold py-2 rounded-lg hover:bg-green-700 transition shadow-md">
            रजिस्टर करें
        </button>
        <button id="registerResendOtpBtn" class="w-full mt-2 text-brand font-bold text-sm hover:underline">
            OTP पुनः भेजें
        </button>
        <p id="regVerifyMessage" class="text-sm text-center mt-3"></p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const sendOtpBtn = document.getElementById('registerSendOtpBtn');
        const verifyOtpBtn = document.getElementById('registerVerifyOtpBtn');
        const resendOtpBtn = document.getElementById('registerResendOtpBtn');
        const regName = document.getElementById('regName');
        const regEmail = document.getElementById('regEmail');
        const regMobile = document.getElementById('regMobile');
        const regOtp = document.getElementById('regOtp');
        const regMessage = document.getElementById('regMessage');
        const regVerifyMessage = document.getElementById('regVerifyMessage');

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Send OTP for Registration
        sendOtpBtn.addEventListener('click', function() {
            const name = regName.value.trim();
            const email = regEmail.value.trim();
            const mobile = regMobile.value.trim();

            if (!name) {
                regMessage.innerHTML = '<span class="text-red-500">कृपया अपना नाम दर्ज करें।</span>';
                return;
            }
            if (!mobile || mobile.length !== 10) {
                regMessage.innerHTML = '<span class="text-red-500">कृपया सही 10-अंकीय मोबाइल नंबर दर्ज करें।</span>';
                return;
            }

            regMessage.innerHTML = '<span class="text-blue-500">OTP भेजा जा रहा है...</span>';
            sendOtpBtn.disabled = true;

            fetch('{{ route("otp.generate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    mobile: mobile,
                    type: 'register'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    regMessage.innerHTML = '<span class="text-green-500">✅ OTP सफलतापूर्वक भेज दिया गया! कृपया अपना OTP दर्ज करें।</span>';
                    // ✅ OTP को Screen पर नहीं दिखा रहे
                    step1.style.display = 'none';
                    step2.style.display = 'block';
                } else {
                    regMessage.innerHTML = '<span class="text-red-500">❌ ' + (data.message || 'OTP भेजने में त्रुटि।') + '</span>';
                }
            })
            .catch(err => {
                regMessage.innerHTML = '<span class="text-red-500">❌ नेटवर्क त्रुटि। कृपया पुनः प्रयास करें।</span>';
            })
            .finally(() => {
                sendOtpBtn.disabled = false;
            });
        });

        // Verify OTP & Register
        verifyOtpBtn.addEventListener('click', function() {
            const mobile = regMobile.value.trim();
            const otp = regOtp.value.trim();

            if (!otp || otp.length !== 6) {
                regVerifyMessage.innerHTML = '<span class="text-red-500">कृपया 6-अंकीय OTP दर्ज करें।</span>';
                return;
            }

            regVerifyMessage.innerHTML = '<span class="text-blue-500">सत्यापन हो रहा है...</span>';
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
                    type: 'register',
                    name: regName.value.trim(),
                    email: regEmail.value.trim()
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    regVerifyMessage.innerHTML = '<span class="text-green-500">✅ रजिस्टर सफल! Redirection हो रहा है...</span>';
                    window.location.href = data.redirect || '/';
                } else {
                    regVerifyMessage.innerHTML = '<span class="text-red-500">❌ ' + (data.message || 'गलत OTP या OTP समाप्त हो गया।') + '</span>';
                }
            })
            .catch(err => {
                regVerifyMessage.innerHTML = '<span class="text-red-500">❌ नेटवर्क त्रुटि। कृपया पुनः प्रयास करें।</span>';
            })
            .finally(() => {
                verifyOtpBtn.disabled = false;
            });
        });

        // Resend OTP for Registration
        resendOtpBtn.addEventListener('click', function() {
            const mobile = regMobile.value.trim();

            if (!mobile || mobile.length !== 10) {
                regVerifyMessage.innerHTML = '<span class="text-red-500">कृपया सही मोबाइल नंबर दर्ज करें।</span>';
                return;
            }

            regVerifyMessage.innerHTML = '<span class="text-blue-500">OTP पुनः भेजा जा रहा है...</span>';
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
                    regVerifyMessage.innerHTML = '<span class="text-green-500">✅ ' + data.message + '</span>';
                } else {
                    regVerifyMessage.innerHTML = '<span class="text-red-500">❌ ' + (data.message || 'OTP पुनः भेजने में त्रुटि।') + '</span>';
                }
            })
            .catch(err => {
                regVerifyMessage.innerHTML = '<span class="text-red-500">❌ नेटवर्क त्रुटि। कृपया पुनः प्रयास करें।</span>';
            })
            .finally(() => {
                resendOtpBtn.disabled = false;
            });
        });
    });
</script>
@endsection