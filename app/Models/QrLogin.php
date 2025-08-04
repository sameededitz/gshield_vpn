<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrLogin extends Model
{
    protected $fillable = [
        'token',
        'user_id',
        'used',
        'expires_at',
    ];

    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function isExpired()
    {
        return now()->greaterThan($this->expires_at);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
