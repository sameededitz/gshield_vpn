<?php

namespace App\Jobs;

use Throwable;
use App\Models\User;
use App\Traits\UsesDynamicSmtp;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailVerification implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, UsesDynamicSmtp;

    public $user;

    public $tries = 3;

    public $timeout = 120;

    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->applySmtpConfig();
        $this->user->sendEmailVerificationNotification();
    }

    public function backoff(): array
    {
        return [3, 6, 10];
    }

    public function retryUntil()
    {
        return now()->addMinutes(5);
    }

    public function failed(?Throwable $exception)
    {
        Log::error('Failed to send email verification: ' . $exception->getMessage());
    }
}
