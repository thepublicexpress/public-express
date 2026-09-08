<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // ✅ Check if user is authenticated
        if (!Auth::check()) {
            // ✅ Redirect to login with proper message
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to continue.',
                    'redirect' => route('login')
                ], 401);
            }
            
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = Auth::user();
        $userRole = $user->role ?? 'guest';

        // ✅ Super Admin has access to everything
        if ($userRole === 'super_admin') {
            Log::info("🛡️ Super Admin access granted", [
                'user_id' => $user->id,
                'email' => $user->email,
                'route' => $request->route()->getName() ?? 'unnamed',
                'url' => $request->fullUrl()
            ]);
            return $next($request);
        }

        // ✅ Check if user has required role
        foreach ($roles as $role) {
            if ($userRole === $role) {
                Log::info("✅ Role access granted", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $userRole,
                    'required_roles' => $roles,
                    'route' => $request->route()->getName() ?? 'unnamed'
                ]);
                return $next($request);
            }
        }

        // ❌ Access Denied - Log the attempt
        Log::warning("🚫 Role access denied", [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $userRole,
            'required_roles' => $roles,
            'route' => $request->route()->getName() ?? 'unnamed',
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ]);

        // ✅ Return appropriate response based on request type
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Required roles: ' . implode(', ', $roles),
                'your_role' => $userRole,
                'required_roles' => $roles
            ], 403);
        }

        // ✅ If this is a reporter route, redirect to reporter dashboard with error
        if ($request->is('reporter/*')) {
            return redirect()->route('reporter.dashboard')
                ->with('error', 'Access Denied. You need ' . implode(', ', $roles) . ' role to access this page.');
        }

        // ✅ If this is an admin route, redirect to admin dashboard with error
        if ($request->is('admin/*')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Access Denied. You need ' . implode(', ', $roles) . ' role to access this page.');
        }

        // ✅ Default: abort with 403
        abort(403, 'Unauthorized access. Required roles: ' . implode(', ', $roles));
    }
}