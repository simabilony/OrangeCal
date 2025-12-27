<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Check if user has active subscription
        $hasActiveSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();

        if (!$hasActiveSubscription && !$user->is_premium) {
            return response()->json([
                'message' => 'This feature requires an active subscription',
                'requires_subscription' => true,
            ], 403);
        }

        return $next($request);
    }
}






