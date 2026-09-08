<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Show Reporter Login Form
     */
    public function showLogin(Request $request)
    {
        $request->session()->regenerateToken();

        return response()
            ->view('reporter.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Show Reporter Registration Form
     */
    public function showRegister()
    {
        $states = State::where('is_active', 1)->orderBy('name')->get();
        $districts = District::where('is_active', 1)->orderBy('name')->get();
        $tehsils = Tehsil::where('is_active', 1)->orderBy('name')->get();
        $blocks = Block::where('is_active', 1)->orderBy('name')->get();

        return view('reporter.register', compact('states', 'districts', 'tehsils', 'blocks'));
    }

    /**
     * Handle Reporter Registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15|unique:users,phone',
            'password' => 'required|min:8|confirmed',
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
            'tehsil_id' => 'nullable|exists:tehsils,id',
            'block_id' => 'nullable|exists:blocks,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'reporter',
            'assigned_state_id' => $request->state_id,
            'assigned_district_id' => $request->district_id,
            'assigned_tehsil_id' => $request->tehsil_id,
            'assigned_block_id' => $request->block_id,
            'is_active' => 0,
            'is_approved' => 0,
            'is_verified' => 1,
        ]);

        Auth::login($user);

        return redirect()->route('reporter.dashboard')->with('success', '✅ रजिस्टर सफल! आपका अकाउंट ऐडमिन द्वारा स्वीकृत होने तक प्रतीक्षा करें।');
    }

    /**
     * Generate OTP for Reporter Login/Register
     */
    public function generateOtp(Request $request)
    {
        $rateLimitKey = 'reporter-otp-generate-' . $request->mobile;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'बहुत अधिक OTP अनुरोध। कृपया 1 घंटे बाद पुनः प्रयास करें।'
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|max:15',
            'type' => 'required|in:login,register',
            'email' => 'nullable|email|max:255',
            'name' => 'required_if:type,register|string|max:255',
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

        $otp = rand(100000, 999999);
        OtpVerification::where('mobile', $mobile)->delete();

        $otpData = OtpVerification::create([
            'mobile' => $mobile,
            'email' => $email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_used' => 0,
            'attempts' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        \Log::info('📦 Reporter OTP Stored', ['id' => $otpData->id, 'mobile' => $mobile]);

        if ($email) {
            $this->sendOtpViaEmail($email, $otp);
        }
        $this->sendOtpViaSms($mobile, $otp);

        RateLimiter::hit($rateLimitKey, 3600);

        $request->session()->flash('otp_sent', true);
        $request->session()->flash('mobile', $mobile);

        return response()->json([
            'success' => true,
            'message' => 'OTP भेज दिया गया!',
            'otp_id' => $otpData->id,
            'show_otp' => false,
        ]);
    }

    /**
     * Verify OTP for Reporter
     */
    public function verifyOtp(Request $request)
    {
        $rateLimitKey = 'reporter-otp-verify-' . $request->mobile;
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

        $otpRecord = OtpVerification::where('mobile', $mobile)
            ->where('otp', $otp)
            ->where('is_used', 0)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            RateLimiter::hit($rateLimitKey, 60);
            return response()->json([
                'success' => false,
                'message' => 'गलत या समाप्त OTP।'
            ], 400);
        }

        $otpRecord->update(['is_used' => 1]);
        RateLimiter::clear($rateLimitKey);

        if ($type == 'register') {
            $email = $request->email ?? $mobile . '@user.thepublicexpress.com';
            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'phone' => $mobile,
                'password' => Hash::make(Str::random(12)),
                'role' => 'reporter',
                'is_active' => 0,
                'is_approved' => 0,
                'is_verified' => 1,
            ]);

            Auth::login($user);
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
                'message' => 'रजिस्टर सफल!',
                'redirect' => route('reporter.dashboard')
            ]);
        }

        $user = User::where('phone', $mobile)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'उपयोगकर्ता नहीं मिला।'
            ], 404);
        }

        Auth::login($user);
    $request->session()->regenerate();
        return response()->json([
            'success' => true,
            'message' => 'लॉगिन सफल!',
            'redirect' => route('reporter.dashboard')
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $rateLimitKey = 'reporter-otp-resend-' . $request->mobile;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            return response()->json([
                'success' => false,
                'message' => 'बहुत अधिक अनुरोध। कृपया 1 घंटे बाद पुनः प्रयास करें।'
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
        OtpVerification::where('mobile', $mobile)->delete();

        $otp = rand(100000, 999999);
        $otpData = OtpVerification::create([
            'mobile' => $mobile,
            'email' => null,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_used' => 0,
            'attempts' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->sendOtpViaSms($mobile, $otp);
        RateLimiter::hit($rateLimitKey, 3600);

        return response()->json([
            'success' => true,
            'message' => 'नया OTP भेज दिया गया!',
            'otp_id' => $otpData->id,
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('reporter.login')->with('success', 'लॉगआउट सफल!');
    }

    /**
     * Show Profile
     */
    public function showProfile()
    {
        $user = Auth::user();
        $states = State::where('is_active', 1)->get();
        $districts = District::where('is_active', 1)->get();
        $tehsils = Tehsil::where('is_active', 1)->get();
        $blocks = Block::where('is_active', 1)->get();
        return view('reporter.profile', compact('user', 'states', 'districts', 'tehsils', 'blocks'));
    }

    /**
     * ✅ UPDATE PROFILE – Email & Phone अब Optional (Nullable) हैं
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // ✅ Validation – email & phone अब nullable हैं (भरना ज़रूरी नहीं)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,   // ✅ Nullable
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $user->id, // ✅ Nullable
            'bio'   => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
            'tehsil_id' => 'nullable|exists:tehsils,id',
            'block_id' => 'nullable|exists:blocks,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // ✅ 1. Always update name & bio
        $user->name = $request->name;
        $user->bio  = $request->bio;

        // ✅ 2. Update email only if provided (filled)
        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        // ✅ 3. Update phone only if provided (filled)
        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }

        // ✅ 4. Location updates (if provided)
        if ($request->has('state_id')) {
            $user->assigned_state_id = $request->state_id;
        }
        if ($request->has('district_id')) {
            $user->assigned_district_id = $request->district_id;
        }
        if ($request->has('tehsil_id')) {
            $user->assigned_tehsil_id = $request->tehsil_id;
        }
        if ($request->has('block_id')) {
            $user->assigned_block_id = $request->block_id;
        }

        // ✅ 5. Photo upload (if provided)
        if ($request->hasFile('photo')) {
            // Delete old photo (if exists)
            if ($user->photo && file_exists(public_path($user->photo))) {
                unlink(public_path($user->photo));
            }

            // Generate new filename & move to public/uploads/reporters/
            $fileName = time() . '_' . $request->file('photo')->getClientOriginalName();
            $request->file('photo')->move(public_path('uploads/reporters'), $fileName);
            $user->photo = 'uploads/reporters/' . $fileName;
        }

        // ✅ 6. Save all changes
        $user->save();

        return redirect()->back()->with('success', '✅ प्रोफाइल सफलतापूर्वक अपडेट हुई! (Bio और फोटो सहित)');
    }

    /**
     * Save Setup (Extra Route)
     */
    public function saveSetup(Request $request)
    {
        return redirect()->back()->with('success', '✅ सेटअप सहेज लिया गया!');
    }

    // ============================================================
    // ✅ PRIVATE HELPERS
    // ============================================================

    private function sendOtpViaEmail($email, $otp)
    {
        try {
            \Mail::raw("Your Reporter OTP for The Public Express is: $otp\n\nThis OTP is valid for 10 minutes.", function ($message) use ($email) {
                $message->to($email)->subject('Reporter OTP - The Public Express');
            });
            \Log::info('✅ Reporter OTP Email sent to ' . $email);
            return true;
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    private function sendOtpViaSms($mobile, $otp)
    {
        try {
            $smsGateway = env('SMS_GATEWAY_EMAIL', 'sms.indiannumber.com');
            $smsEmail = $mobile . '@' . $smsGateway;
            \Mail::raw("Your Reporter OTP for The Public Express is: $otp", function ($message) use ($smsEmail) {
                $message->to($smsEmail)->subject('OTP Verification');
            });
            \Log::info('📲 Reporter SMS sent to ' . $smsEmail);
            return true;
        } catch (\Exception $e) {
            \Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }
}