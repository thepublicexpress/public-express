<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role_id == 4) {
            return redirect()->route('reporter.dashboard');
        }
        return view('reporter.login');
    }

    public function showRegister()
    {
        return view('reporter.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            if ($user->role_id == 4 && $user->is_active) {
                return redirect()->route('reporter.dashboard');
            }
            Auth::logout();
            return back()->withErrors(['email' => 'Reporter account not found or inactive']);
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|digits:10|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 4,
            'is_active' => true,
        ]);

        Auth::login($user);
        return redirect()->route('reporter.dashboard')->with('success', 'Registration successful!');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('reporter.login');
    }
}