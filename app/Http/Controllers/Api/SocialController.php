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

            // Check if the user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // If user exists, update all details except email
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            } else {
                // If user does not exist, create a new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
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

            $user = User::where('apple_id', $appleId)->orWhere('email', $email)->first();

            if ($user) {
                // Update Apple ID if needed
                $user->update(['apple_id' => $appleId]);
            } else {
                // Create a new user
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