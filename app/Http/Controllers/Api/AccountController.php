<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendPasswordReset;
use App\Jobs\SendEmailVerification;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as RulesPassword;

class AccountController extends Controller
{
    public function resendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all()
            ], 400);
        }

        /** @var \App\Models\User $user **/
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => true,
                'message' => 'Email already Verified'
            ], 200);
        }

        // SendEmailVerification::dispatch($user)->delay(now()->addSeconds(5));
        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'A new verification link has been sent to the email address you provided during registration.'
        ], 200);
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found!'
            ], 404);
        }

        // Validate signature
        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 403);
        }

        // Validate hash
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully!'
        ], 200);
    }

    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all()
            ], 400);
        }

        /** @var \App\Models\User $user **/
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found!'
            ], 404);
        }

        $token = Password::createToken($user);

        SendPasswordReset::dispatch($user, $token)->delay(now()->addSeconds(5));

        return response()->json([
            'status' => true,
            'message' => 'Password reset link sent. Please check your inbox.'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required',
            'password' => [
                'required',
                'confirmed',
                RulesPassword::min(8)
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all()
            ], 422);
        }

        try {
            // Decrypt the encrypted email
            $decodedEmail = urldecode($request->email);
            $decryptedEmail = decrypt($decodedEmail);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'The provided link is invalid or has expired. Please request a new password reset link.'
            ], 400);
        }

        $status = Password::reset(
            [
                'email' => $decryptedEmail,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
            ],
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => true,
                'message' => __('passwords.reset'),
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => __($status),
        ], 400);
    }
}
