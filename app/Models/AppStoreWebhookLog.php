<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppStoreWebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_type',
        'subtype',
        'notification_uuid',
        'original_transaction_id',
        'bundle_id',
        'status',
        'error_message',
        'payload',
        'decoded_payload',
        'notification_timestamp',
    ];

    protected $casts = [
        'payload' => 'array',
        'decoded_payload' => 'array',
        'notification_timestamp' => 'datetime',
    ];

    public function markAsProcessed(): void
    {
        $this->update(['status' => 'processed']);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }
}