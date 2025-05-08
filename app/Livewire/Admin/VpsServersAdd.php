<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\VpsServer;

class VpsServersAdd extends Component
{
    public $name, $ip_address, $username, $port, $domain, $status, $private_key, $password;

    protected function rules()
    {
        return [
            'name' => 'required',
            'ip_address' => 'required',
            'username' => 'required',
            'port' => 'required',
            // 'domain' => 'required',
            'status' => 'required',
            'private_key' => 'nullable|required_without:password',
            'password' => 'nullable|required_without:private_key',
        ];
    }

    public function store()
    {
        $this->validate();

        VpsServer::create([
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'username' => $this->username,
            'port' => $this->port,
            'domain' => $this->domain,
            'status' => $this->status,
            'private_key' => $this->private_key,
            'password' => $this->password,
        ]);

        $this->reset(['name', 'ip_address', 'username', 'port', 'status', 'private_key', 'password']);

        $this->dispatch('sweetToast', type: 'success', message: 'VPS Server created successfully');
        return $this->dispatch('redirect', url: route('vps-servers.all'));
    }

    public function render()
    {
        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.vps-servers-add')
            ->extends('layouts.app')
            ->section('content');
    }
}
