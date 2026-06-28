<?php
// app/Http/Middleware/ReporterMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ReporterMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('reporter.login')
                ->with('error', 'Please login to access reporter panel.');
        }

        $user = auth()->user();
        
        if (!in_array($user->role, ['reporter', 'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter', 'admin', 'super_admin'])) {
            auth()->logout();
            return redirect()->route('reporter.login')
                ->with('error', 'Unauthorized access. Reporter privileges required.');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('reporter.login')
                ->with('error', 'Your account is inactive. Please contact admin.');
        }

        if (in_array($user->role, ['reporter', 'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter']) && !$user->is_approved) {
            auth()->logout();
            return redirect()->route('reporter.login')
                ->with('error', 'Your account is not approved yet. Please wait for admin approval.');
        }

        return $next($request);
    }
}