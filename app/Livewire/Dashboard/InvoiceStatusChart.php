<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class InvoiceStatusChart extends Component
{
    public array $chartData = [];
    public array $labels = [];

    public function mount()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {
        // Cache for 15 minutes
        $stats = Cache::remember('dashboard_invoice_status_stats', 900, function() {
            // Get counts grouped by status for the current year
            $counts = Invoice::whereYear('created_at', date('Y'))
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $mapping = [
                'Paid' => $counts['paid'] ?? 0,
                'Sent' => $counts['sent'] ?? 0,
                'Overdue' => $counts['overdue'] ?? 0,
                'Draft' => $counts['draft'] ?? 0,
            ];

            // Only return statuses that have data to make chart look cleaner
            // or return all if we want fixed colors
            return $mapping;
        });

        $this->labels = array_keys($stats);
        $this->chartData = array_values($stats);
    }

    public function render()
    {
        return view('livewire.dashboard.invoice-status-chart');
    }
}
