<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Purchase;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class AllPurchases extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;

    #[Url]
    public $statusFilter = '';
    #[Url]
    public $amountFilter = '';

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
    public function updatingAmountFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(
            'statusFilter',
            'amountFilter'
        );
    }

    public function updateStatus($purchaseId, $status)
    {
        $purchase = Purchase::findOrFail($purchaseId);
        $purchase->status = $status;
        $purchase->save();

        $this->dispatch('sweetAlert', title: 'Updated!', message: 'Purchase status has been updated.', type: 'success');
    }

    public function deletePurchase($purchaseId)
    {
        $purchase = Purchase::findOrFail($purchaseId);
        $purchase->delete();

        $this->dispatch('sweetAlert', title: 'Deleted!', message: 'Purchase has been deleted successfully.', type: 'success');
    }

    public function render()
    {
        $purchases = Purchase::query()
            ->when($this->search, fn($query) => $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $this->search . '%')))
            ->when($this->statusFilter, fn($query) => $query->where('status', $this->statusFilter))
            ->when($this->amountFilter, fn($query) => $query->where('amount_paid', '<=', $this->amountFilter))
            ->latest()
            ->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-purchases', compact('purchases'))
            ->extends('layouts.app')
            ->section('content');
    }
}
