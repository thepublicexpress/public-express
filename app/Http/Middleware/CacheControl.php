<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CacheControl
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // 🔥 EXTREME CACHE DISABLE - Har possible header
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, post-check=0, pre-check=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $response->headers->set('ETag', md5(uniqid() . microtime()));
        
        return $response;
    }
}