<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppStoreAPIService
{
    private AppleJWTService $jwtService;
    private string $baseUrl;

    public function __construct(AppleJWTService $jwtService)
    {
        $this->jwtService = $jwtService;
        
        $environment = config('appstore.environment');
        $this->baseUrl = config("appstore.endpoints.{$environment}");
    }

    /**
     * Get subscription status from Apple
     */
    public function getSubscriptionStatus(string $originalTransactionId): array
    {
        $jwt = $this->jwtService->generateAuthToken();
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $jwt,
            'Content-Type' => 'application/json',
            'User-Agent' => 'Laravel-AppStore-Client/1.0'
        ])->get("{$this->baseUrl}/inApps/v1/subscriptions/{$originalTransactionId}");

        if ($response->successful()) {
            return $response->json();
        }

        $error = "Failed to get subscription status: HTTP {$response->status()}";
        Log::error($error, [
            'transaction_id' => $originalTransactionId,
            'response_body' => $response->body()
        ]);

        throw new \Exception($error);
    }

    /**
     * Get transaction history
     */
    public function getTransactionHistory(string $originalTransactionId, ?string $revision = null): array
    {
        $jwt = $this->jwtService->generateAuthToken();
        
        $url = "{$this->baseUrl}/inApps/v1/history/{$originalTransactionId}";
        if ($revision) {
            $url .= "?revision={$revision}";
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $jwt,
            'Content-Type' => 'application/json',
            'User-Agent' => 'Laravel-AppStore-Client/1.0'
        ])->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        $error = "Failed to get transaction history: HTTP {$response->status()}";
        Log::error($error, [
            'transaction_id' => $originalTransactionId,
            'response_body' => $response->body()
        ]);

        throw new \Exception($error);
    }

    /**
     * Get order lookup information
     */
    public function getOrderLookup(string $orderId): array
    {
        $jwt = $this->jwtService->generateAuthToken();
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $jwt,
            'Content-Type' => 'application/json',
            'User-Agent' => 'Laravel-AppStore-Client/1.0'
        ])->get("{$this->baseUrl}/inApps/v1/lookup/{$orderId}");

        if ($response->successful()) {
            return $response->json();
        }

        $error = "Failed to get order lookup: HTTP {$response->status()}";
        Log::error($error, [
            'order_id' => $orderId,
            'response_body' => $response->body()
        ]);

        throw new \Exception($error);
    }

    /**
     * Request a test notification (sandbox only)
     */
    public function requestTestNotification(): array
    {
        if (config('appstore.environment') !== 'sandbox') {
            throw new \Exception('Test notifications are only available in sandbox environment');
        }

        $jwt = $this->jwtService->generateAuthToken();
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $jwt,
            'Content-Type' => 'application/json',
            'User-Agent' => 'Laravel-AppStore-Client/1.0'
        ])->post("{$this->baseUrl}/inApps/v1/notifications/test");

        if ($response->successful()) {
            return $response->json();
        }

        $error = "Failed to request test notification: HTTP {$response->status()}";
        Log::error($error, [
            'response_body' => $response->body()
        ]);

        throw new \Exception($error);
    }
}