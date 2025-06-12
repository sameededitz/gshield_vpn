<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\JWK;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppleJWTService
{
    private string $issuerId;
    private string $keyId;
    private string $privateKeyPath;
    private string $bundleId;

    public function __construct()
    {
        $this->issuerId = config('appstore.issuer_id');
        $this->keyId = config('appstore.key_id');
        $this->privateKeyPath = storage_path('app/apple/keys/SubscriptionKey_8GRW5PZLBX.p8');
        $this->bundleId = config('appstore.bundle_id');
    }

    /**
     * Generate JWT token for Apple App Store Server API
     */
    public function generateAuthToken(): string
    {
        $header = [
            'alg' => 'ES256',
            'kid' => $this->keyId,
            'typ' => 'JWT'
        ];

        $now = time();
        $payload = [
            'iss' => $this->issuerId,
            'iat' => $now,
            'exp' => $now + (20 * 60), // 20 minutes
            'aud' => 'appstoreconnect-v1',
            'bid' => $this->bundleId
        ];

        $privateKey = $this->getPrivateKey();
        
        return JWT::encode($payload, $privateKey, 'ES256', $this->keyId, $header);
    }

    /**
     * Verify and decode Apple's signed payload
     */
    public function verifyAndDecodePayload(string $signedPayload): array
    {
        try {
            // Get the header to determine which key to use
            $header = $this->getJWTHeader($signedPayload);
            $keyId = $header['kid'] ?? null;

            if (!$keyId) {
                throw new \Exception('No key ID found in JWT header');
            }

            // Get Apple's public keys
            $publicKeys = $this->getApplePublicKeys();
            
            if (!isset($publicKeys[$keyId])) {
                throw new \Exception("Key ID '{$keyId}' not found in Apple's public keys");
            }

            // Verify and decode
            $decoded = JWT::decode($signedPayload, new Key($publicKeys[$keyId], 'ES256'));
            
            return json_decode(json_encode($decoded), true);

        } catch (\Exception $e) {
            Log::error('JWT verification failed', [
                'error' => $e->getMessage(),
                'payload_preview' => substr($signedPayload, 0, 100) . '...'
            ]);
            throw new \Exception('JWT verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Decode JWT payload without verification (for debugging)
     */
    public function decodePayloadUnsafe(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new \Exception('Invalid JWT format');
        }

        $payload = base64_decode($parts[1]);
        $decoded = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to decode JWT payload: ' . json_last_error_msg());
        }

        return $decoded;
    }

    /**
     * Get Apple's public keys with caching
     */
    private function getApplePublicKeys(): array
    {
        return Cache::remember('apple_public_keys', 3600, function () {
            try {
                $response = Http::timeout(30)->get(config('appstore.public_keys_url'));
                
                if (!$response->successful()) {
                    throw new \Exception('Failed to fetch Apple public keys: HTTP ' . $response->status());
                }

                $keyData = $response->json();
                
                if (!isset($keyData['keys'])) {
                    throw new \Exception('Invalid response format from Apple keys endpoint');
                }

                return JWK::parseKeySet($keyData);

            } catch (\Exception $e) {
                Log::error('Failed to fetch Apple public keys', [
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Get JWT header
     */
    private function getJWTHeader(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new \Exception('Invalid JWT format');
        }

        $header = base64_decode($parts[0]);
        $decoded = json_decode($header, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to decode JWT header: ' . json_last_error_msg());
        }

        return $decoded;
    }

    /**
     * Get private key content
     */
    private function getPrivateKey(): string
    {
        if (!file_exists($this->privateKeyPath)) {
            throw new \Exception("Private key file not found at: {$this->privateKeyPath}");
        }

        $privateKey = file_get_contents($this->privateKeyPath);
        
        if ($privateKey === false) {
            throw new \Exception("Failed to read private key file");
        }

        return $privateKey;
    }

    /**
     * Validate configuration
     */
    public function validateConfiguration(): array
    {
        $errors = [];

        if (empty($this->issuerId)) {
            $errors[] = 'Apple Issuer ID is not configured';
        }

        if (empty($this->keyId)) {
            $errors[] = 'Apple Key ID is not configured';
        }

        if (empty($this->bundleId)) {
            $errors[] = 'Apple Bundle ID is not configured';
        }

        if (!file_exists($this->privateKeyPath)) {
            $errors[] = "Private key file not found at: {$this->privateKeyPath}";
        }

        return $errors;
    }
}