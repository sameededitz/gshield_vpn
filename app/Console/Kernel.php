<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\TestAppleConnection::class,
        Commands\CheckSubscriptionStatus::class,
        Commands\SendTestNotification::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Optional: Schedule subscription cleanup or status checks
        $schedule->command('model:prune', ['--model' => 'App\\Models\\AppStoreWebhookLog'])
                 ->daily()
                 ->description('Clean up old webhook logs');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}