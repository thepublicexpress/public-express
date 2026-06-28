<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please login to access admin panel.');
        }

        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'super_admin', 'state_admin', 'district_admin', 'tehsil_admin', 'block_admin'])) {
            auth()->logout();
            return redirect()->route('admin.login')
                ->with('error', 'Unauthorized access. Admin privileges required.');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('admin.login')
                ->with('error', 'Your account is inactive. Please contact admin.');
        }

        if (!$user->is_approved) {
            auth()->logout();
            return redirect()->route('admin.login')
                ->with('error', 'Your account is not approved yet.');
        }

        return $next($request);
    }
}