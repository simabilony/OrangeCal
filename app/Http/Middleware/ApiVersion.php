<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersion
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $version = 'v1'): Response
    {
        $request->merge(['api_version' => $version]);
        
        return $next($request);
    }
}






