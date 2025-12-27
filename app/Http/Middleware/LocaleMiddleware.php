<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language', 'ar');
        
        // Normalize locale (ar, en)
        if (str_starts_with($locale, 'ar')) {
            $locale = 'ar';
        } elseif (str_starts_with($locale, 'en')) {
            $locale = 'en';
        } else {
            $locale = 'ar'; // default
        }

        app()->setLocale($locale);

        return $next($request);
    }
}






