<?php

namespace App\Http\Controllers\Api;

use App\Models\QrLogin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRLoginController extends Controller
{
    public function requestLogin()
    {
        $token = Str::uuid();
        $expiresAt = now()->addMinutes(5);

        QrLogin::create([
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        $encryptedToken = Crypt::encryptString($token);

        $qrPayload = [
            'type' => 'tv-login',
            'token' => $encryptedToken,
        ];

        $qrData = json_encode($qrPayload);

        // Generate Base64 PNG
        $qrCodeObject = QrCode::format('svg')->size(300)->generate($qrData);

        // Convert HtmlString object to actual string
        $qrCode = $qrCodeObject->toHtml();

        return response()->json([
            'encrypted_token' => $encryptedToken,
            'qr_svg' => $qrCode,
            'expires_at' => $expiresAt->toIso8601String(),
        ]);
    }

    public function confirmScan(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'encrypted_token' => 'required|string'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validated->errors()->all()
            ], 422);
        }

        try {
            $token = Crypt::decryptString($request->encrypted_token);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token'
            ], 401);
        }

        $qr = QrLogin::where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $qr->update([
            'user_id' => Auth::id(),
            'used' => true,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'QR code confirmed successfully!',
        ]);
    }

    public function checkStatus(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'encrypted_token' => 'required|string'
        ]);
        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validated->errors()->all()
            ], 422);
        }

        // Decrypt the token
        $encrypted_token = $request->input('encrypted_token');

        try {
            $token = Crypt::decryptString($encrypted_token);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token'
            ], 401);
        }

        $qr = QrLogin::where('token', $token)->first();

        if (!$qr || $qr->isExpired()) {
            return response()->json([
                'status' => false,
                'message' => 'QR code expired or not found.'
            ], 404);
        }

        if ($qr->used && $qr->user_id) {
            /** @var \App\Models\User $user **/
            $user = $qr->user;
            $tvToken = $user->createToken('tv', ['tv'])->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully!',
                'user' => new UserResource($user),
                'access_token' => $tvToken,
                'token_type' => 'Bearer',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'QR code not yet used or user not found.'
        ], 202);
    }
}
