<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyController extends Controller
{
    public function verify(Request $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user() ? Auth::user() : User::find($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException('Invalid verification link');
        }

        if ($user->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? response()->json([
                    'status' => true,
                    'message' => 'Email already verified'
                ], 200)
                : view('auth.verify', [
                    'status' => 'Email already verified'
                ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $request->wantsJson()
            ? response()->json([
                'status' => true,
                'message' => 'Email verified successfully'
            ], 200)
            : view('auth.verify', [
                'status' => 'Email verified successfully'
            ]);
    }
}
