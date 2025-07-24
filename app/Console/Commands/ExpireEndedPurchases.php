<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use Illuminate\Console\Command;

class ExpireEndedPurchases extends Command
{
    protected $signature = 'purchases:expire-ended';
    protected $description = 'Expire all purchases that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        $expiredCount = Purchase::where('status', ['active', 'cancelled'])
            ->whereNotNull('end_date')
            ->where('end_date', '<', $now)
            ->update(['status' => 'expired']);

        $this->info("Expired {$expiredCount} purchases.");
    }
}
