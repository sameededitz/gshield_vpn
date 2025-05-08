<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendPasswordReset;
use App\Models\PasswordResetCode;
use App\Jobs\SendEmailVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
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

        SendEmailVerification::dispatch($user)->delay(now()->addSeconds(5));

        return response()->json([
            'status' => true,
            'message' => 'A new verification link has been sent to the email address you provided during registration.'
        ], 200);
    }

    public function sendResetToken(Request $request)
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

        // Generate 6-digit code
        $code = random_int(100000, 999999);

        // Store code in cache for 15 minutes
        PasswordResetCode::updateOrCreate(
            ['email' => $request->email],
            [
                'code' => $code,
                'expires_at' => now()->addMinutes(15)
            ]
        );

        SendPasswordReset::dispatch($user, $code)->delay(now()->addSeconds(5));

        return response()->json([
            'status' => true,
            'message' => 'Password reset link sent. Please check your inbox.'
        ], 200);
    }

    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $record = PasswordResetCode::where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid code!',
            ], 422);
        }

        if ($record->expires_at < now()) {
            return response()->json([
                'status' => false,
                'message' => 'Code expired!',
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Code verified successfully.',
            'data' => [
                'email' => $record->email,
                'code' => $record->code,
            ]
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'password' => [
                'required',
                'confirmed',
                RulesPassword::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all()
            ], 422);
        }

        $record = PasswordResetCode::where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Code!'
            ], 422);
        }

        if ($record->expires_at < now()) {
            return response()->json([
                'status' => false,
                'message' => 'Code expired!'
            ], 422);
        }

        $user = User::where('email', $record->email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found!'
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ]);

        // Delete the record from the database
        $record->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully.'
        ], 200);
    }
}
