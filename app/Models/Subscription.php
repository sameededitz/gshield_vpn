<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_transaction_id',
        'web_order_line_item_id',
        'product_id',
        'subscription_group_identifier',
        'status',
        'purchased_at',
        'expires_at',
        'grace_period_expires_at',
        'auto_renew_status',
        'auto_renew_product_id',
        'cancellation_reason',
        'price_increase_status',
        'latest_transaction_info',
        'latest_renewal_info',
        'metadata',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
        'grace_period_expires_at' => 'datetime',
        'auto_renew_status' => 'boolean',
        'latest_transaction_info' => 'array',
        'latest_renewal_info' => 'array',
        'metadata' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere('expires_at', '<=', now());
        });
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->expires_at && 
               $this->expires_at->isFuture();
    }

    public function isInGracePeriod(): bool
    {
        return $this->status === 'billing_grace_period' &&
               $this->grace_period_expires_at &&
               $this->grace_period_expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return !$this->isActive() && !$this->isInGracePeriod();
    }

    public function getRemainingDays(): int
    {
        if (!$this->expires_at) {
            return 0;
        }

        return max(0, now()->diffInDays($this->expires_at, false));
    }
}