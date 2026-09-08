<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // ✅ सभी Admin रोल्स को अनुमति दें
        $allowedRoles = ['admin', 'super_admin', 'state_admin', 'district_admin', 'tehsil_admin'];
        
        if (!in_array(Auth::user()->role, $allowedRoles)) {
            abort(403, 'Unauthorized access. You must be an admin to access this page.');
        }

        return $next($request);
    }
}