<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    public function __construct(private DashboardRepository $repository) {}

    public function getStats(): array
    {
        return [
            'total_clients'        => $this->repository->totalClients(),
            'total_clinics'        => $this->repository->totalClinics(),
            'active_subscriptions' => $this->repository->activeSubscriptions(),
            'unpaid_invoices'      => $this->repository->unpaidInvoices(),
            'monthly_revenue'      => $this->repository->monthlyRevenue(),
        ];
    }
}
