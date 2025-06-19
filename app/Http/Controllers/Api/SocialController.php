<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;

class SocialController extends Controller
{
    public function google(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all()
            ], 400);
        }

        $accessToken = $request->input('token');
        try {
            /** @disregard @phpstan-ignore-line */
            $googleUser = Socialite::driver('google')->userFromToken($accessToken);

            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $name = $googleUser->getName();

            // First, check by google_id
            $user = User::where('google_id', $googleId)->first();

            if (!$user && $email) {
                // Then fallback to email
                $existing = User::where('email', $email)->first();
                if ($existing) {
                    $existing->update(['google_id' => $googleId]);
                    $user = $existing;
                }
            }

            // Create new if no user found
            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => Hash::make(Str::random(10)),
                    'email_verified_at' => now(),
                ]);
            }

            // Log the user in
            Auth::login($user);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully!',
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Error logging in with Google Api: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error logging in with Google: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function apple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all()
            ], 400);
        }

        $id_token = $request->input('id_token');

        try {
            /** @disregard @phpstan-ignore-line */
            $appleUser = Socialite::driver('apple')->stateless()->userFromToken($id_token);

            Log::info('Apple User Data: ', (array) $appleUser);

            // Extract user details
            $appleId = $appleUser->id;
            $email = $appleUser->email;
            $name = $appleUser->name ?? $email;

            $user = User::where('apple_id', $appleId)->first();

            if (!$user && $email) {
                $existing = User::where('email', $email)->first();
                if ($existing) {
                    $existing->update(['apple_id' => $appleId]);
                    $user = $existing;
                }
            }

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'apple_id' => $appleId,
                    'password' => Hash::make(Str::random(10)),
                    'email_verified_at' => now(),
                ]);
            }

            // Log the user in
            Auth::login($user);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully!',
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Error logging in with Apple Api: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error logging in with Apple: ' . $e->getMessage(),
            ], 500);
        }
    }
}
