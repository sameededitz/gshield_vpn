<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class AllPlans extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;

    #[Url]
    public $priceFilter = '';
    #[Url]
    public $durationUnitFilter = '';

    public $planId;
    public $name;
    public $price;
    public $duration;
    public $duration_unit;
    public $description;
    public $isEdit = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'duration_unit' => 'required|string|in:day,week,month,year',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function resetForm()
    {
        $this->reset([
            'planId',
            'name',
            'price',
            'duration',
            'duration_unit',
            'description',
        ]);
        $this->isEdit = false;
        $this->resetValidation();
    }

    public function editPlan($planId)
    {
        $this->resetForm();
        $this->isEdit = true;

        $plan = Plan::findOrFail($planId);
        $this->planId = $plan->id;
        $this->name = $plan->name;
        $this->price = $plan->price;
        $this->duration = $plan->duration;
        $this->duration_unit = $plan->duration_unit;
        $this->description = $plan->description;
    }

    public function savePlan()
    {
        $this->validate();

        if ($this->isEdit) {
            $plan = Plan::findOrFail($this->planId);
            $plan->update([
                'name' => $this->name,
                'price' => $this->price,
                'duration' => $this->duration,
                'duration_unit' => $this->duration_unit,
                'description' => $this->description,
            ]);
            $message = 'Plan updated successfully.';
        } else {
            Plan::create([
                'name' => $this->name,
                'price' => $this->price,
                'duration' => $this->duration,
                'duration_unit' => $this->duration_unit,
                'description' => $this->description,
            ]);
            $message = 'Plan created successfully.';
        }

        $this->dispatch('closeModel');
        $this->dispatch('sweetAlert', title: 'Success!', message: $message, type: 'success');
        $this->resetPage();
        $this->resetForm();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function updatingPriceFilter()
    {
        $this->resetPage();
    }
    public function updatingDurationUnitFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset('priceFilter', 'durationUnitFilter');
    }

    public function deletePlan($planId)
    {
        $plan = Plan::findOrFail($planId);
        $plan->delete();

        $this->dispatch('sweetAlert', title: 'Deleted!', message: 'Plan has been deleted successfully.', type: 'success');
    }

    public function render()
    {
        $plans = Plan::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->priceFilter, fn($query) => $query->where('price', '<=', $this->priceFilter))
            ->when($this->durationUnitFilter, fn($query) => $query->where('duration_unit', $this->durationUnitFilter))
            ->latest()
            ->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-plans', compact('plans'))
            ->extends('layouts.app')
            ->section('content');
    }
}
