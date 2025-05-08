<?php

namespace App\Livewire\Admin;

use App\Models\Server;
use Livewire\Component;
use App\Models\SubServer;
use App\Models\VpsServer;

class SubServerEdit extends Component
{
    public Server $server;
    public SubServer $subServer;
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

    public function mount(Server $server, SubServer $subServer)
    {
        $this->server = $server;
        $this->subServer = $subServer;
        $this->name = $subServer->name;
        $this->status = $subServer->status;
        $this->vps_server = $subServer->vps_server_id;
    }

    public function store()
    {
        $this->validate();

        $this->subServer->update([
            'name' => $this->name,
            'status' => $this->status,
            'vps_server_id' => $this->vps_server,
        ]);

        return redirect()->intended(route('all.sub-servers', $this->server))->with('message', 'Sub Server updated successfully.');
    }

    public function render()
    {
        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.sub-server-edit', [
            'vpsServers' => VpsServer::all(),
        ])->extends('layouts.app')->section('content');
    }
}
