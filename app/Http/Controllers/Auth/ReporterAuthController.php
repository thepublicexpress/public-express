<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OtpVerification;
use App\Models\District;
use App\Models\Tehsil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            // Agar user already login hai toh dashboard redirect karo
            if (Auth::user()->role_id == 4) {
                return redirect()->route('reporter.dashboard');
            }
        }
        return view('reporter.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10'
        ]);

        // Check if user already exists and is blocked
        $user = User::where('phone', $request->phone)->first();
        if ($user && $user->is_active == false) {
            return response()->json([
                'success' => false,
                'message' => 'आपका खाता ब्लॉक कर दिया गया है। कृपया एडमिन से संपर्क करें।'
            ], 403);
        }

        $otp = rand(1000, 9999);

        // Delete old OTPs for this phone
        OtpVerification::where('phone', $request->phone)->delete();

        // Create new OTP
        OtpVerification::create([
            'phone'      => $request->phone,
            'otp'        => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used'    => false,
        ]);

        // Log OTP for development (production mein SMS API lagana hai)
        Log::info("====================================");
        Log::info("The Public Express - OTP for Reporter");
        Log::info("Phone: {$request->phone}");
        Log::info("OTP: {$otp}");
        Log::info("Valid for 10 minutes");
        Log::info("====================================");

        return response()->json([
            'success' => true,
            'message' => 'OTP सफलतापूर्वक भेज दिया गया है।',
            'dev_otp' => config('app.debug') ? $otp : null,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'otp'   => 'required|digits:4',
        ]);

        // Find valid OTP
        $record = OtpVerification::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->where('is_used', false)
            ->where('expires_at', '>=', Carbon::now())
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'OTP गलत है या समय समाप्त हो गया है। पुनः प्रयास करें।'
            ], 422);
        }

        // Mark OTP as used
        $record->update(['is_used' => true]);

        // Find or create user
        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            [
                'role_id'           => 4, // Reporter role
                'name'              => '', // Will be filled in setup
                'password'          => bcrypt($request->phone),
                'is_active'         => true,
                'phone_verified_at' => Carbon::now(),
                'points'            => 0,
                'wallet_balance'    => 0,
            ]
        );

        // Login the user
        Auth::login($user);

        // Determine redirect based on profile completion
        $needsSetup = empty($user->name) || empty($user->district_id);
        
        $redirect = $needsSetup 
            ? route('reporter.setup') 
            : route('reporter.dashboard');

        return response()->json([
            'success'  => true,
            'message'  => 'लॉगिन सफल!',
            'redirect' => $redirect,
        ]);
    }

    public function showSetup()
    {
        // Check if user is already setup
        if (Auth::user()->name && Auth::user()->district_id) {
            return redirect()->route('reporter.dashboard');
        }
        
        $districts = District::where('is_active', true)->orderBy('name')->get();
        return view('reporter.setup', compact('districts'));
    }

    public function setupProfile(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|min:2|max:255',
            'district_id' => 'required|exists:districts,id',
            'tehsil_id'   => 'nullable|exists:tehsils,id',
            'avatar'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();
        
        $data = [
            'name'        => $request->name,
            'district_id' => $request->district_id,
            'tehsil_id'   => $request->tehsil_id,
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        return redirect()->route('reporter.dashboard')
            ->with('success', 'प्रोफाइल सेट हो गई! अब आप खबरें भेज सकते हैं। 🎉');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('reporter.login')
            ->with('success', 'आप लॉग आउट हो गए हैं।');
    }
}