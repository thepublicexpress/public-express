<?php
// app/Http/Middleware/CheckLocationPermission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLocationPermission
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
        $user = auth()->user();

        // If user is not logged in
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Admin has full access to all locations
        if ($user->role === 'admin') {
            return $next($request);
        }

        // ===== REPORTER: Force location from user profile =====
        if ($user->role === 'reporter') {
            // If reporter has assigned tehsil, force it
            if ($user->assigned_tehsil_id) {
                $request->merge(['tehsil_id' => $user->assigned_tehsil_id]);
                $request->merge(['district_id' => $user->assigned_district_id]);
                $request->merge(['state_id' => $user->assigned_state_id]);
            }
            
            // If reporter has assigned district (but no tehsil)
            if ($user->assigned_district_id && !$user->assigned_tehsil_id) {
                $request->merge(['district_id' => $user->assigned_district_id]);
                $request->merge(['state_id' => $user->assigned_state_id]);
            }
            
            // If reporter has assigned state (but no district)
            if ($user->assigned_state_id && !$user->assigned_district_id && !$user->assigned_tehsil_id) {
                $request->merge(['state_id' => $user->assigned_state_id]);
            }
            
            return $next($request);
        }

        // ===== DISTRICT BUREAU CHIEF =====
        if ($user->approval_level === 'district') {
            $districtId = $request->input('district_id');
            
            // Check if trying to access different district
            if ($districtId && $districtId != $user->assigned_district_id) {
                return redirect()->back()->with('error', 'You can only access your assigned district.');
            }
            
            // If no district provided, force assigned district
            if (!$districtId) {
                $request->merge(['district_id' => $user->assigned_district_id]);
                $request->merge(['state_id' => $user->assigned_state_id]);
            }
            
            return $next($request);
        }

        // ===== STATE HEAD =====
        if ($user->approval_level === 'state') {
            $stateId = $request->input('state_id');
            
            // Check if trying to access different state
            if ($stateId && $stateId != $user->assigned_state_id) {
                return redirect()->back()->with('error', 'You can only access your assigned state.');
            }
            
            // If no state provided, force assigned state
            if (!$stateId) {
                $request->merge(['state_id' => $user->assigned_state_id]);
            }
            
            return $next($request);
        }

        // ===== NATIONAL LEVEL =====
        if ($user->approval_level === 'national') {
            // National level has access to all locations
            return $next($request);
        }

        // ===== DEFAULT: Allow if no restrictions =====
        // If user has no location restrictions, allow access
        return $next($request);
    }
}