<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$role): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user || !$user->hasAnyRole(...$role)) {
            return $request->wantsJson()
                ? response()->json([
                    'message' => 'Unauthorized. You do not have the required role to access this resource.',
                ], Response::HTTP_UNAUTHORIZED)
                : redirect()->route('login')->withErrors([
                    'message' => 'Unauthorized. You do not have the required role to access this resource.',
                ]);
        }

        return $next($request);
    }
}
