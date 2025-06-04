<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user->isBanned()) {
            $user->tokens()->delete();

            return response()->json([
                'status' => false,
                'message' => 'Your account is banned. Please contact support for more information.'
            ], 403);
        }

        if(!$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'Your email is not verified. Please verify your email to continue.'
            ], 403);
        }

        return $next($request);
    }
}
