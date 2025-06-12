<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\AppStoreNotificationService;
use App\Services\AppleJWTService;

class AppStoreWebhookController extends Controller
{
    private AppStoreNotificationService $notificationService;
    private AppleJWTService $jwtService;

    public function __construct(
        AppStoreNotificationService $notificationService,
        AppleJWTService $jwtService
    ) {
        $this->notificationService = $notificationService;
        $this->jwtService = $jwtService;
    }

    /**
     * Handle App Store Server notification webhook
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            // Log incoming webhook for debugging
            Log::info('App Store webhook received', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'content_type' => $request->header('Content-Type'),
                'payload_size' => strlen($request->getContent()),
                'timestamp' => now()->toISOString(),
            ]);

            // Validate request has required data
            $payload = $request->all();
            if (empty($payload) || !isset($payload['signedPayload'])) {
                Log::warning('Invalid webhook payload received', [
                    'payload' => $payload,
                    'raw_content' => $request->getContent(),
                ]);
                
                return response()->json([
                    'error' => 'Invalid payload format'
                ], 400);
            }

            // Verify signature if enabled
            if (config('appstore.webhook.verify_signature', true)) {
                try {
                    $this->jwtService->verifyAndDecodePayload($payload['signedPayload']);
                } catch (\Exception $e) {
                    Log::warning('Webhook signature verification failed', [
                        'error' => $e->getMessage(),
                        'ip' => $request->ip(),
                    ]);
                    
                    return response()->json([
                        'error' => 'Invalid signature'
                    ], 401);
                }
            }

            // Process the notification
            $processed = $this->notificationService->processNotification($payload);

            if ($processed) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Notification processed successfully'
                ], 200);
            }

            return response()->json([
                'error' => 'Failed to process notification'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'error' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Handle webhook verification (for testing)
     */
    public function verify(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'verified',
            'timestamp' => now()->toISOString(),
            'environment' => config('appstore.environment'),
        ]);
    }
}