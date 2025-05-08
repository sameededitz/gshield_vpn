<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubServer extends Model
{
    protected $fillable = ['server_id', 'vps_server_id', 'name', 'status'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
    public function vpsServer()
    {
        return $this->belongsTo(VpsServer::class);
    }
    public function isActive()
    {
        return $this->status === 1;
    }
}
