<?php

namespace App\Livewire\Widgets;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;

class UsersCount extends Component
{
    public $months = [];
    public $counts = [];

    public $recentUsers = [];

    public function mount()
    {
        $this->prepareChartData();

        $this->recentUsers = User::latest()->take(10)->get();
    }

    public function prepareChartData()
    {
        $now = Carbon::now();
        $start = $now->copy()->subMonths(5)->startOfMonth();

        $users = User::where('created_at', '>=', $start)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('M');
            });

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i)->format('M');
            $this->months[] = $month;
            $this->counts[] = isset($users[$month]) ? $users[$month]->count() : 0;
        }
    }

    public function render()
    {
        return view('livewire.widgets.users-count');
    }
}
