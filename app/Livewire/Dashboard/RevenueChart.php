<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RevenueChart extends Component
{
    public array $chartData = [];
    public array $categories = [];

    public function mount()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {
        // Cache for 60 minutes
        $data = Cache::remember('dashboard_revenue_trend', 3600, function() {
            $trend = [];
            $labels = [];

            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $labels[] = $month->format('M'); // 'Oct', 'Nov', etc.

                $sum = Invoice::where('status', 'paid')
                        ->whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->sum('total');

                $trend[] = $sum;
            }
            return ['trend' => $trend, 'labels' => $labels];
        });

        $this->chartData = $data['trend'];
        $this->categories = $data['labels'];
    }

    public function render()
    {
        return view('livewire.dashboard.revenue-chart');
    }
}
