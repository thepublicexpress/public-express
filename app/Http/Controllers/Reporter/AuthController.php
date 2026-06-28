<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\District;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Show Login Form
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role_id == 4) {
            return redirect()->route('reporter.dashboard');
        }
        return view('reporter.login');
    }

    // Show Registration Form
    public function showRegister()
    {
        if (Auth::check() && Auth::user()->role_id == 4) {
            return redirect()->route('reporter.dashboard');
        }
        return view('reporter.register');
    }

    // Handle Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->role_id == 4 && $user->is_active == 1) {
                return redirect()->route('reporter.dashboard')->with('success', 'Welcome back!');
            }
            
            Auth::logout();
            return back()->withErrors(['email' => 'You are not authorized as a reporter.']);
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    // Handle Registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits:10|unique:users,phone',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 4,
            'is_active' => true,
            'points' => 0,
            'wallet_balance' => 0,
        ]);

        Auth::login($user);

        return redirect()->route('reporter.dashboard')->with('success', 'Registration successful! Welcome to The Public Express.');
    }

    // Show Profile Page
    public function showProfile()
    {
        $user = Auth::user();
        $districts = District::where('is_active', true)->orderBy('name')->get();
        return view('reporter.profile', compact('user', 'districts'));
    }

    // Update Profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'district_id' => 'nullable|exists:districts,id',
            'tehsil_id' => 'nullable|exists:tehsils,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'upi_id' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $request->name,
            'district_id' => $request->district_id,
            'tehsil_id' => $request->tehsil_id,
            'upi_id' => $request->upi_id,
        ];

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('reporter.profile')->with('success', 'प्रोफाइल अपडेट हो गई!');
    }

    // Handle Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('reporter.login')->with('success', 'You have been logged out successfully.');
    }
}