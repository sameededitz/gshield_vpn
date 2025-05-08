<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $fillable = [
        'host',
        'port',
        'username',
        'password',
        'from_address',
        'from_name',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'host' => 'string',
            'port' => 'integer',
            'username' => 'string',
            'password' => 'string',
            'from_address' => 'string',
            'from_name' => 'string',
        ];
    }
}
