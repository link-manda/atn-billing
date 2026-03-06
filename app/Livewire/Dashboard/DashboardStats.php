<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Services\DashboardService;

class DashboardStats extends Component
{
    public int $total_clients = 0;
    public int $total_clinics = 0;
    public int $active_subscriptions = 0;
    public int $unpaid_invoices = 0;
    public float $monthly_revenue = 0;

    public function mount(DashboardService $service): void
    {
        $stats = $service->getStats();

        $this->total_clients        = $stats['total_clients'];
        $this->total_clinics        = $stats['total_clinics'];
        $this->active_subscriptions = $stats['active_subscriptions'];
        $this->unpaid_invoices      = $stats['unpaid_invoices'];
        $this->monthly_revenue      = $stats['monthly_revenue'];
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-stats');
    }
}