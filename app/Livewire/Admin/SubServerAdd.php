<?php

namespace App\Livewire\Admin;

use App\Models\Server;
use Livewire\Component;
use App\Models\VpsServer;

class SubServerAdd extends Component
{
    public Server $server;
    public $name, $status;
    public $vps_server;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'vps_server' => 'required|exists:vps_servers,id',
        ];
    }

    public function mount(Server $server)
    {
        $this->server = $server;
    }

    public function store()
    {
        $this->validate();

        $this->server->subServers()->create([
            'name' => $this->name,
            'status' => $this->status,
            'vps_server_id' => $this->vps_server,
        ]);

        return redirect()->intended(route('all.sub-servers', $this->server))->with('message', 'Sub Server added successfully.');
    }

    public function render()
    {
        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.sub-server-add', [
            'vpsServers' => VpsServer::all(),
        ])->extends('layouts.app')->section('content');
    }
}
