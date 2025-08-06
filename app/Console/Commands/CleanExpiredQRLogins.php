<?php

namespace App\Console\Commands;

use App\Models\QrLogin;
use Illuminate\Console\Command;

class CleanExpiredQRLogins extends Command
{
    protected $signature = 'qr:clean-expired';

    protected $description = 'Deletes expired and old used QR logins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete unused expired tokens (older than 10 mins ago)
        $unused = QrLogin::where('used', false)
            ->where('expires_at', '<', now()->subMinutes(10))
            ->delete();

        // Optionally: delete used tokens older than 7 days
        $used = QrLogin::where('used', true)
            ->where('updated_at', '<', now()->subDays(7))
            ->delete();

        $this->info("Expired tokens cleaned: $unused");
        $this->info("Old used tokens cleaned: $used");
    }
}
