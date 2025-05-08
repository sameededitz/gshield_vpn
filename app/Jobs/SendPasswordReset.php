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

class SendPasswordReset implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, UsesDynamicSmtp;

    public $user;
    public $code;

    public $tries = 3;
    public $timeout = 120;
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->applySmtpConfig();
        $this->user->sendPasswordResetNotification($this->code);
    }

    public function backoff(): array
    {
        return [3, 6, 10];
    }

    public function retryUntil()
    {
        return now()->addMinutes(5); // Retry for 5 minutes
    }

    public function failed(?Throwable $exception)
    {
        Log::error('Failed to send (' . $this->user->email . ') password reset email: ' . $exception->getMessage());
    }
}