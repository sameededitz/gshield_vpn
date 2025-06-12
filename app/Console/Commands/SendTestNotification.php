<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AppStoreAPIService;

class SendTestNotification extends Command
{
    protected $signature = 'appstore:test-notification';
    protected $description = 'Request a test notification from Apple (sandbox only)';

    public function handle(AppStoreAPIService $apiService)
    {
        try {
            if (config('appstore.environment') !== 'sandbox') {
                $this->error('Test notifications are only available in sandbox environment');
                return 1;
            }

            $this->info('Requesting test notification from Apple...');
            
            $response = $apiService->requestTestNotification();
            
            $this->info('Test notification requested successfully:');
            $this->line(json_encode($response, JSON_PRETTY_PRINT));

            $this->newLine();
            $this->info('Check your webhook endpoint for the incoming test notification.');

            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to request test notification: ' . $e->getMessage());
            return 1;
        }
    }
}