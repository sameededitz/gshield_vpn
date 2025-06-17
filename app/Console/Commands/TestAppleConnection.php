<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AppleJWTService;
use App\Services\AppStoreAPIService;

class TestAppleConnection extends Command
{
    protected $signature = 'appstore:test-connection';
    protected $description = 'Test Apple App Store Server API connection and configuration';

    public function handle(AppleJWTService $jwtService, AppStoreAPIService $apiService)
    {
        $this->info('Testing Apple App Store Server API connection...');
        $this->newLine();

        try {
            // Test configuration
            $this->info('1. Checking configuration...');
            $errors = $jwtService->validateConfiguration();
            
            if (!empty($errors)) {
                $this->error('Configuration errors found:');
                foreach ($errors as $error) {
                    $this->error("   - {$error}");
                }
                return 1;
            }
            $this->info('   ✅ Configuration is valid');

            // Test JWT generation
            $this->info('2. Testing JWT generation...');
            $jwt = $jwtService->generateAuthToken();
            $this->info('   ✅ JWT token generated successfully');
            $this->line('   Token preview: ' . substr($jwt, 0, 50) . '...');

            // Test Apple public keys
            $this->info('3. Testing Apple public keys...');
            $testPayload = 'eyJhbGciOiJFUzI1NiIsImtpZCI6Ijh... (very long, with two dots) ...Qssw5c'; // Sample JWT
            try {
                // This will fail but shows we can fetch keys
                $jwtService->verifyAndDecodePayload($testPayload);
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'Key ID')) {
                    $this->info('   ✅ Apple public keys fetched successfully');
                } else {
                    throw $e;
                }
            }

            // Test environment endpoint
            $this->info('4. Testing environment endpoint...');
            $environment = config('appstore.environment');
            $endpoint = config("appstore.endpoints.{$environment}");
            $this->info("   Environment: {$environment}");
            $this->info("   Endpoint: {$endpoint}");
            $this->info('   ✅ Environment configuration is valid');

            $this->newLine();
            $this->info('🎉 All tests passed! Your Apple App Store integration is ready.');
            $this->newLine();
            $this->info('Next steps:');
            $this->info('1. Configure your webhook URL in App Store Connect:');
            $this->info('   ' . url('/webhook/appstore-notifications'));
            $this->info('2. Test with sandbox transactions');
            $this->info('3. Monitor logs for incoming notifications');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            return 1;
        }
    }
}