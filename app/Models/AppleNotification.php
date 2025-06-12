<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppleNotification extends Model
{
    protected $casts = [
    'raw_payload' => 'array',
];

}
