<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\VpsServer;
use Livewire\WithPagination;

class AllVpsServers extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 5;

    public $statusFilter = '';

    public VpsServer $VpsServer;
    public $name, $ip_address, $username, $port, $domain, $status, $private_key, $password;

    protected function rules()
    {
        return [
            'name' => 'required',
            'ip_address' => 'required|ip',
            'username' => 'required',
            'port' => 'required|numeric',
            // 'domain' => 'required|url',
            'status' => 'required|boolean',
            'private_key' => 'nullable|required_without:password',
            'password' => 'nullable|required_without:private_key',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function editVpsServer($id)
    {
        $server = VpsServer::findOrFail($id);

        $this->reset(['VpsServer', 'name', 'ip_address', 'username', 'port', 'status', 'private_key', 'domain', 'password']);

        $this->VpsServer = $server;
        $this->name = $server->name;
        $this->ip_address = $server->ip_address;
        $this->username = $server->username;
        $this->port = $server->port;
        $this->domain = $server->domain;
        $this->status = $server->status;
        $this->private_key = $server->private_key;
        $this->password = $server->password;
    }

    public function updateVpsServer()
    {
        $this->validate();

        $this->VpsServer->update([
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'username' => $this->username,
            'port' => $this->port,
            'domain' => $this->domain,
            'status' => $this->status,
            'private_key' => $this->private_key,
            'password' => $this->password,
        ]);

        $this->reset(['VpsServer', 'name', 'ip_address', 'username', 'port', 'status', 'private_key', 'domain', 'password']);

        $this->dispatch('closeEditModal');
    }

    public function deleteVpsServer($id)
    {
        $vpsServer = VpsServer::findOrFail($id);
        $vpsServer->delete();
        $this->dispatch('sweetAlert', type: 'success', message: 'VPS Server deleted successfully', title: 'Deleted!');
    }

    public function render()
    {
        $vpsServers = VpsServer::where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                ->orWhere('username', 'like', '%' . $this->search . '%')
                ->orWhere('port', 'like', '%' . $this->search . '%')
                ->orWhere('domain', 'like', '%' . $this->search . '%')
                ->orWhere('created_at', 'like', '%' . $this->search . '%');
        })->when($this->statusFilter !== '', function ($query) {
            $query->where('status', (bool) $this->statusFilter);
        })->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-vps-servers', compact('vpsServers'))
            ->extends('layouts.app')
            ->section('content');
    }
}
