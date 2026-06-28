<?php
// app/Http/Middleware/PermissionMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Admin has all permissions
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check specific permissions
        switch ($permission) {
            case 'can_approve':
                if (!$user->can_approve) {
                    abort(403, 'You do not have permission to approve news.');
                }
                break;

            case 'is_admin':
                if (!$user->isAdmin()) {
                    abort(403, 'Admin access required.');
                }
                break;

            case 'is_reporter':
                if (!$user->isReporter()) {
                    abort(403, 'Reporter access required.');
                }
                break;

            case 'can_access_location':
                $stateId = $request->input('state_id');
                $districtId = $request->input('district_id');
                $tehsilId = $request->input('tehsil_id');
                if (!$user->canAccessLocation($stateId, $districtId, $tehsilId)) {
                    abort(403, 'You do not have permission to access this location.');
                }
                break;

            case 'can_write_category':
                $categoryId = $request->input('category_id');
                if (!$user->canWriteCategory($categoryId)) {
                    abort(403, 'You do not have permission to write in this category.');
                }
                break;

            default:
                abort(403, 'Invalid permission check.');
        }

        return $next($request);
    }
}