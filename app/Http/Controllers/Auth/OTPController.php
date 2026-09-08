<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class OTPController extends Controller
{
    /**
     * Show OTP Registration Form
     */
    public function showRegisterForm()
    {
        return view('auth.otp-register');
    }

    /**
     * Show OTP Login Form
     */
    public function showLoginForm()
    {
        return view('auth.otp-login');
    }

    /**
     * Generate and Send OTP – SECURE VERSION
     */
    public function generateOtp(Request $request)
    {
        // ✅ Rate Limiting: Max 5 OTP requests per hour per mobile
        $rateLimitKey = 'otp-generate-' . $request->mobile;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'बहुत अधिक OTP अनुरोध। कृपया 1 घंटे बाद पुनः प्रयास करें।'
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'name' => 'required_if:type,register|string|max:255',
            'type' => 'required|in:login,register',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $mobile = $request->mobile;
        $type = $request->type;
        $email = null;

        // ✅ Check if user exists for login
        if ($type == 'login') {
            $user = User::where('phone', $mobile)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'इस मोबाइल नंबर से कोई खाता नहीं मिला। कृपया पहले रजिस्टर करें।'
                ], 404);
            }
            $email = $user->email;
        }

        // ✅ Check if user already exists for register
        if ($type == 'register') {
            $existingUser = User::where('phone', $mobile)->first();
            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'यह मोबाइल नंबर पहले से रजिस्टर है। कृपया लॉगिन करें।'
                ], 409);
            }
            $email = $request->email;
        }

        // ✅ Generate OTP
        $otp = rand(100000, 999999);

        // ✅ Delete old OTPs
        OtpVerification::where('mobile', $mobile)->delete();

        // ✅ Store OTP
        $otpData = OtpVerification::create([
            'mobile' => $mobile,
            'email' => $email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_used' => 0,
            'attempts' => 0,
            'resend_count' => 0,
            'last_resend_at' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        \Log::info('📦 OTP Stored', [
            'id' => $otpData->id,
            'mobile' => $mobile,
            'email' => $email,
            'otp' => $otp,
        ]);

        // ✅ Send OTP via Email (always)
        if ($email) {
            $this->sendOtpViaEmail($email, $otp);
        }

        // ✅ Send OTP via SMS (ONLY if enabled in .env)
        if (env('SMS_ENABLED', false)) {
            $this->sendOtpViaSms($mobile, $otp);
        } else {
            \Log::info('ℹ️ SMS sending skipped (SMS_ENABLED=false)');
        }

        // ✅ Rate Limiter hit
        RateLimiter::hit($rateLimitKey, 3600);

        return response()->json([
            'success' => true,
            'message' => 'OTP सफलतापूर्वक भेज दिया गया!',
            'otp_id' => $otpData->id,
            'show_otp' => true,
        ]);
    }

    /**
     * Verify OTP – SECURE VERSION
     */
    public function verifyOtp(Request $request)
    {
        \Log::info('🔐 Verify OTP Request Received', $request->all());

        // ✅ Rate Limiting: Max 5 verification attempts per minute
        $rateLimitKey = 'otp-verify-' . $request->mobile;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'बहुत अधिक प्रयास। कृपया 1 मिनट बाद पुनः प्रयास करें।'
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|max:15',
            'otp' => 'required|string|size:6',
            'type' => 'required|in:login,register',
            'name' => 'required_if:type,register|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $mobile = $request->mobile;
        $otp = $request->otp;
        $type = $request->type;

        // ✅ Find OTP – Only if not used and not expired
        $otpRecord = OtpVerification::where('mobile', $mobile)
            ->where('otp', $otp)
            ->where('is_used', 0)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            RateLimiter::hit($rateLimitKey, 60);
            
            // ✅ Check if OTP exists but expired
            $expiredOtp = OtpVerification::where('mobile', $mobile)
                ->where('otp', $otp)
                ->where('is_used', 0)
                ->first();

            if ($expiredOtp) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP समाप्त हो गया है। कृपया नया OTP जनरेट करें।'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'गलत या अमान्य OTP। कृपया पुनः प्रयास करें।'
            ], 400);
        }

        // ✅ Check IP Address consistency (optional but recommended)
        if ($otpRecord->ip_address !== $request->ip()) {
            \Log::warning('⚠️ OTP IP mismatch', [
                'stored_ip' => $otpRecord->ip_address,
                'request_ip' => $request->ip()
            ]);
        }

        // ✅ Mark OTP as used
        $otpRecord->update([
            'is_used' => 1,
        ]);

        RateLimiter::clear($rateLimitKey);

        // ✅ Registration flow
        if ($type == 'register') {
            $email = $request->email ?? $mobile . '@user.thepublicexpress.com';
            
            try {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $email,
                    'phone' => $mobile,
                    'password' => Hash::make(Str::random(12)),
                    'role' => 'subscriber',
                    'is_active' => 1,
                    'is_approved' => 1,
                    'is_verified' => 1,
                    'points' => 0,
                    'wallet_balance' => 0,
                ]);

                \Log::info('✅ User Created', ['user_id' => $user->id, 'mobile' => $mobile]);

                Auth::login($user);
                $request->session()->regenerate();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful!',
                    'redirect' => url('/'),
                ]);

            } catch (\Exception $e) {
                \Log::error('❌ Registration Failed', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed: ' . $e->getMessage(),
                ], 500);
            }
        }

        // ✅ Login flow
        $user = User::where('phone', $mobile)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found. Please register first.'
            ], 404);
        }

        \Log::info('✅ User Found for Login', ['user_id' => $user->id, 'role' => $user->role]);

        Auth::login($user);
        $request->session()->regenerate();
        
        // ✅ Role based redirect
        $redirectUrl = url('/');
        $adminRoles = ['admin', 'super_admin', 'state_admin', 'district_admin', 'tehsil_admin'];
        if (in_array($user->role, $adminRoles)) {
            $redirectUrl = url('/admin/dashboard');
        } elseif (str_contains($user->role, 'reporter')) {
            $redirectUrl = url('/reporter/dashboard');
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'redirect' => $redirectUrl,
        ]);
    }

    /**
     * Resend OTP – SECURE VERSION
     */
    public function resendOtp(Request $request)
    {
        // ✅ Rate Limiting: Max 3 resend attempts per hour
        $rateLimitKey = 'otp-resend-' . $request->mobile;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            return response()->json([
                'success' => false,
                'message' => 'बहुत अधिक OTP पुनः भेजने के अनुरोध। कृपया 1 घंटे बाद पुनः प्रयास करें।'
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $mobile = $request->mobile;
        
        // ✅ Delete all old OTPs
        OtpVerification::where('mobile', $mobile)->delete();

        // ✅ Generate new OTP
        $otp = rand(100000, 999999);

        // ✅ Store new OTP
        $otpData = OtpVerification::create([
            'mobile' => $mobile,
            'email' => null,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_used' => 0,
            'attempts' => 0,
            'resend_count' => 1,
            'last_resend_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // ✅ Send OTP via Email (if email exists)
        // (We don't have email here, so skip; only SMS if enabled)
        if (env('SMS_ENABLED', false)) {
            $this->sendOtpViaSms($mobile, $otp);
        } else {
            \Log::info('ℹ️ SMS resend skipped (SMS_ENABLED=false)');
        }

        RateLimiter::hit($rateLimitKey, 3600);

        return response()->json([
            'success' => true,
            'message' => 'नया OTP सफलतापूर्वक भेज दिया गया!',
            'otp_id' => $otpData->id,
            'show_otp' => true,
        ]);
    }

    /**
     * Check OTP Status
     */
    public function checkStatus(Request $request)
    {
        $mobile = $request->mobile;
        $otpRecord = OtpVerification::where('mobile', $mobile)
            ->where('is_used', 0)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpRecord) {
            return response()->json([
                'exists' => true,
                'expires_at' => $otpRecord->expires_at,
            ]);
        }

        return response()->json([
            'exists' => false,
            'message' => 'No pending OTP found.'
        ]);
    }

    // ============================================================
    // ✅ PRIVATE METHODS (Email & SMS)
    // ============================================================

    /**
     * Send OTP via Email
     */
    private function sendOtpViaEmail($email, $otp)
    {
        try {
            \Mail::raw("Your OTP for The Public Express is: $otp\n\nThis OTP is valid for 10 minutes.\n\nThank you,\nThe Public Express Team", function ($message) use ($email) {
                $message->to($email)
                        ->subject('Your OTP for The Public Express');
            });
            \Log::info('✅ Email OTP sent to ' . $email);
            return true;
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send OTP via SMS (using email gateway) – ONLY called if SMS_ENABLED=true
     */
    private function sendOtpViaSms($mobile, $otp)
    {
        try {
            $smsGateway = env('SMS_GATEWAY_EMAIL', 'sms.indiannumber.com');
            $smsEmail = $mobile . '@' . $smsGateway;
            
            \Mail::raw("Your OTP for The Public Express is: $otp", function ($message) use ($smsEmail) {
                $message->to($smsEmail)
                        ->subject('OTP Verification');
            });
            
            \Log::info('📲 SMS sent via email gateway to ' . $smsEmail);
            return true;
        } catch (\Exception $e) {
            \Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }
}