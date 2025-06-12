<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionStatus extends Component
{
    public $status;

    public function mount()
    {
        $user = Auth::user();
        $this->status = $user->subscription_status; // Assuming you store it
    }

    public function render()
    {
        return view('livewire.subscription-status');
    }
}