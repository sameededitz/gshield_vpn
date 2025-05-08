<?php

namespace App\Livewire\Admin;

use App\Models\Server;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AllServers extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;

    #[Url]
    public $statusFilter = '';
    #[Url]
    public $typeFilter = '';
    #[Url]
    public $platformFilter = '';

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

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingPlatformFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset([
            'statusFilter',
            'typeFilter',
            'platformFilter',
        ]);
    }

    public function deleteServer($serverId)
    {
        $server = Server::findOrFail($serverId);
        $server->clearMediaCollection('image');
        $server->delete();

        $this->dispatch('sweetAlert', title: 'Deleted!', message: 'Server has been deleted successfully.', type: 'success');
    }

    public function render()
    {
        $servers = Server::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->typeFilter, fn($query) => $query->where('type', $this->typeFilter))
            ->when($this->platformFilter, function ($query) {
                return $query->where($this->platformFilter, true);
            })
            ->when($this->statusFilter !== '', function ($query) {
                return $query->where('status', (bool) $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-servers', compact('servers'))
            ->extends('layouts.app')
            ->section('content');
    }
}
