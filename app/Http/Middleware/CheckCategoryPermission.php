<?php
// app/Http/Middleware/CheckCategoryPermission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCategoryPermission
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

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Admin has full access
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Reporter: Check if category is allowed
        if ($user->role === 'reporter') {
            $categoryId = $request->input('category_id');
            
            if ($categoryId && !$user->canWriteCategory($categoryId)) {
                return redirect()->back()->with('error', 'You are not allowed to write in this category.');
            }
        }

        return $next($request);
    }
}