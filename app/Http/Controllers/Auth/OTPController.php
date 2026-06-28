<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OTPController extends Controller
{
    public function showOtpLogin()
    {
        return view('auth.otp-login');
    }

    public function sendOTP(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $mobile = $request->mobile;

        // Check if already registered
        $user = User::where('phone', $mobile)->where('otp_verified', true)->first();
        if ($user) {
            return response()->json([
                'success' => false,
                'message' => 'This number is already registered. Please login.',
                'registered' => true,
            ]);
        }

        // Rate limiting
        $recentRequests = OtpVerification::where('mobile', $mobile)
            ->where('created_at', '>', now()->subHour())
            ->count();

        if ($recentRequests >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP requests. Please try after 1 hour.',
            ], 429);
        }

        // Generate OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Store in database
        OtpVerification::create([
            'mobile' => $mobile,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
            'attempts' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // ===== SEND OTP VIA EMAIL =====
        $emailSent = $this->sendOTPEmail($mobile, $otp);

        // ===== SEND OTP VIA SMS (Fallback) =====
        $smsSent = $this->sendEmailToSMS($mobile, $otp);

        Log::info("OTP Generated", [
            'mobile' => $mobile,
            'otp' => $otp,
            'email_sent' => $emailSent,
            'sms_sent' => $smsSent,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully! Check your email and SMS.',
            'otp' => $otp, // DEVELOPMENT ONLY
            'mobile' => $mobile,
            'email_sent' => $emailSent,
            'sms_sent' => $smsSent,
        ]);
    }

    /**
     * Send OTP via Email (Using Laravel Mail)
     */
    private function sendOTPEmail($mobile, $otp)
    {
        try {
            // Get user's email if exists
            $user = User::where('phone', $mobile)->first();
            $email = $user ? $user->email : null;

            // If no email, try to use email-to-sms
            if (!$email) {
                Log::info("No email found for {$mobile}, sending via SMS only");
                return false;
            }

            $subject = "🔐 Your OTP for The Public Express";
            
            $htmlContent = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
                    .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                    .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #c62828; }
                    .header h1 { color: #c62828; font-size: 24px; margin: 0; }
                    .header p { color: #666; font-size: 14px; margin: 5px 0 0; }
                    .otp-box { background: #f0f0f0; padding: 20px; text-align: center; font-size: 36px; font-weight: bold; letter-spacing: 8px; border-radius: 8px; margin: 20px 0; color: #c62828; }
                    .footer { text-align: center; color: #999; font-size: 12px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
                    .warning { color: #666; font-size: 13px; text-align: center; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>📰 द पब्लिक एक्सप्रेस</h1>
                        <p>OTP Verification</p>
                    </div>
                    <p style='text-align:center;font-size:16px;color:#333;'>Your OTP for mobile verification is:</p>
                    <div class='otp-box'>{$otp}</div>
                    <p style='text-align:center;color:#666;'>This OTP is valid for <strong>10 minutes</strong>.</p>
                    <p style='text-align:center;color:#666;font-size:13px;'>If you didn't request this, please ignore this email.</p>
                    <div class='footer'>
                        <p>&copy; 2026 द पब्लिक एक्सप्रेस. All rights reserved.</p>
                        <p style='font-size:11px;'>This is an automated message, please do not reply.</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            // Using Laravel Mail
            Mail::send([], [], function ($message) use ($email, $subject, $htmlContent) {
                $message->to($email)
                        ->subject($subject)
                        ->html($htmlContent);
            });

            Log::info("Email OTP sent to {$email}");
            return true;

        } catch (\Exception $e) {
            Log::error("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send OTP via Email-to-SMS Gateway (Fallback)
     */
    private function sendEmailToSMS($mobile, $otp)
    {
        $message = "Your OTP for The Public Express is: {$otp}. Valid for 10 minutes.";
        $subject = "Your OTP for The Public Express";
        
        $gateways = [
            "{$mobile}@sms.indiannumber.com",
            "{$mobile}@txt.att.net",
            "{$mobile}@tmomail.net",
            "{$mobile}@vtext.com",
            "{$mobile}@msg.fi",
        ];

        $headers = "From: noreply@thepublicexpress.com\r\n";
        $headers .= "Reply-To: support@thepublicexpress.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        foreach ($gateways as $to) {
            try {
                if (@mail($to, $subject, $message, $headers)) {
                    Log::info("SMS sent via email gateway to {$to}");
                    return true;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return false;
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp' => 'required|digits:6',
        ]);

        $mobile = $request->mobile;
        $otp = $request->otp;

        $otpRecord = OtpVerification::where('mobile', $mobile)
            ->where('otp', $otp)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        $otpRecord->update(['is_used' => true]);

        $user = User::where('phone', $mobile)->first();

        if ($user) {
            $user->update([
                'otp_verified' => true,
                'mobile_verified_at' => now(),
            ]);

            auth()->login($user);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully!',
                'exists' => true,
            ]);
        }

        Session::put('otp_verified', true);
        Session::put('mobile', $mobile);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified! Complete your profile.',
            'exists' => false,
        ]);
    }

    public function resendOTP(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $mobile = $request->mobile;

        $recentResends = OtpVerification::where('mobile', $mobile)
            ->where('created_at', '>', now()->subHour())
            ->count();

        if ($recentResends >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Too many attempts. Try after 1 hour.',
            ], 429);
        }

        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        OtpVerification::create([
            'mobile' => $mobile,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
            'attempts' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->sendOTPEmail($mobile, $otp);
        $this->sendEmailToSMS($mobile, $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP resent successfully!',
            'otp' => $otp,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'mobile' => 'required|digits:10|unique:users,phone',
            'district_id' => 'nullable|exists:districts,id',
            'state_id' => 'nullable|exists:states,id',
        ]);

        if (!Session::get('otp_verified') || Session::get('mobile') !== $request->mobile) {
            return back()->with('error', 'OTP verification required.');
        }

        $subscriberId = 'SUB' . date('Y') . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->mobile,
            'password' => Hash::make(rand(10000000, 99999999)),
            'role' => 'subscriber',
            'is_subscriber' => true,
            'is_active' => true,
            'is_approved' => true,
            'is_verified' => true,
            'otp_verified' => true,
            'mobile_verified_at' => now(),
            'subscriber_id' => $subscriberId,
            'district_id' => $request->district_id,
            'state_id' => $request->state_id,
            'points' => 0,
            'wallet_balance' => 0,
        ]);

        Session::forget(['otp_verified', 'mobile']);

        auth()->login($user);

        return redirect()->route('home')
            ->with('success', 'Welcome to The Public Express!');
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $otpRecord = OtpVerification::where('mobile', $request->mobile)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        return response()->json([
            'has_valid_otp' => (bool) $otpRecord,
            'expires_in' => $otpRecord ? now()->diffInSeconds($otpRecord->expires_at) : 0,
        ]);
    }
}