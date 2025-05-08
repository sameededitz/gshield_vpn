<?php

namespace App\Livewire\Widgets;

use Carbon\Carbon;
use App\Models\Plan;
use Livewire\Component;
use App\Models\Purchase;

class SalesAnalytic extends Component
{
    public $totalSalesPercent;
    public $weekSales;
    public $monthSales;
    public $yearSales;

    public $boughtPlans = [];

    public $topPlans = [];

    public function mount()
    {
        $this->calculateSalesOverview();
        $this->fetchTopPlans();

        $this->topBoughtPlans();
    }

    public function calculateSalesOverview()
    {
        $this->weekSales = Purchase::whereBetween('created_at', [now()->startOfWeek(), now()])->sum('amount_paid');
        $this->monthSales = Purchase::whereMonth('created_at', now()->month)->sum('amount_paid');
        $this->yearSales = Purchase::whereYear('created_at', now()->year)->sum('amount_paid');

        // For radial chart: % of goal (you can change the 1000 to your target)
        $target = 1000;
        $this->totalSalesPercent = min(100, round(($this->monthSales / $target) * 100));
    }

    public function fetchTopPlans()
    {
        $plans = Purchase::selectRaw('plan_id, COUNT(*) as total')
            ->groupBy('plan_id')
            ->orderByDesc('total')
            ->take(4)
            ->get();

        foreach ($plans as $p) {
            $plan = Plan::find($p->plan_id);
            if ($plan) {
                $this->topPlans[] = [
                    'label' => $plan->name,
                    'value' => $p->total,
                ];
            }
        }
    }

    public function topBoughtPlans()
    {
        $this->boughtPlans = Plan::select('plans.id', 'plans.name', 'plans.price')
            ->join('purchases', 'purchases.plan_id', '=', 'plans.id')
            ->where('purchases.created_at', '>=', Carbon::now()->subDays(30))
            ->where('purchases.status', 'active')
            ->groupBy('plans.id', 'plans.name', 'plans.price')
            ->selectRaw('COUNT(purchases.id) as total_sales, SUM(purchases.amount_paid) as total_revenue')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.widgets.sales-analytic');
    }
}
