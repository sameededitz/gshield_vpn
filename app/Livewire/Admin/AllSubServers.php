<?php

namespace App\Livewire\Admin;

use App\Models\Server;
use Livewire\Component;
use App\Models\SubServer;
use Livewire\WithPagination;

class AllSubServers extends Component
{
    use WithPagination;

    public Server $server;

    public $search = '';
    public $perPage = 5;

    public $statusFilter = '';

    public function mount(Server $server)
    {
        $this->server = $server;
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function deleteSubServer($subServerId)
    {
        $subServer = SubServer::findOrFail($subServerId);
        $subServer->delete();

        $this->dispatch('sweetAlert', title: 'Deleted!', message: 'Sub Server has been deleted successfully.', type: 'success');
    }

    public function render()
    {
        $subServers = $this->server->subServers()
            ->with('vpsServer:id,name,username,ip_address')
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->statusFilter, fn($query) => $query->where('status', $this->statusFilter))
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-sub-servers', compact('subServers'))
            ->extends('layouts.app')
            ->section('content');
    }
}
