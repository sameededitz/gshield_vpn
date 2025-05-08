<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VpsServer extends Model
{
    protected $fillable = [
        'name',
        'username',
        'ip_address',
        'private_key',
        'password',
        'port',
        'domain',
        'status',
    ];

    protected $hidden = [
        'private_key',
        'password',
    ];

    public function isActive()
    {
        return $this->status === 1;
    }
}
