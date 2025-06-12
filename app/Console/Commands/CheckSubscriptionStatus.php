<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AppStoreAPIService;

class CheckSubscriptionStatus extends Command
{
    protected $signature = 'appstore:check-subscription {transaction_id : The original transaction ID}';
    protected $description = 'Check subscription status from Apple';

    public function handle(AppStoreAPIService $apiService)
    {
        $transactionId = $this->argument('transaction_id');

        try {
            $this->info("Checking subscription status for transaction: {$transactionId}");
            
            $response = $apiService->getSubscriptionStatus($transactionId);
            
            $this->info('Subscription Status Response:');
            $this->line(json_encode($response, JSON_PRETTY_PRINT));

            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to check subscription status: ' . $e->getMessage());
            return 1;
        }
    }
}