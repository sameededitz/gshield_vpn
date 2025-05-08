<?php

namespace App\Livewire\Widgets;

use App\Models\Plan;
use App\Models\User;
use App\Models\Server;
use Livewire\Component;
use App\Models\VpsServer;

class Stats extends Component
{
    public $totalVpsServers;
    public $totalServers;
    public $totalPlans;
    public $totalUsers;

    public $userChangePercentage;

    public function mount()
    {
        $this->totalVpsServers = VpsServer::count();
        $this->totalServers = Server::count();
        $this->totalPlans = Plan::count();
        $this->totalUsers = User::where('role', 'user')->count();

        $this->userChangePercentage = $this->getWeeklyChangePercentage(User::class);
    }

    public function render()
    {
        return view('livewire.widgets.stats');
    }

    protected function getWeeklyChangePercentage($model)
    {
        $now = now();

        $thisWeekCount = $model::whereBetween('created_at', [
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek(),
        ])->count();

        $lastWeekCount = $model::whereBetween('created_at', [
            $now->copy()->subWeek()->startOfWeek(),
            $now->copy()->subWeek()->endOfWeek(),
        ])->count();

        if ($lastWeekCount == 0) {
            return $thisWeekCount > 0 ? 100 : 0;
        }

        return round((($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100);
    }
}
