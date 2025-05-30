<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeSession extends Model
{
    protected $fillable = [
        'user_id',
        'purchase_id',
        'payment_intent',
    ];

    protected $casts = [
        'payment_intent' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
