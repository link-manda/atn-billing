<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Clinic;
use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\Payment;

class DashboardRepository
{
    public function totalClients(): int
    {
        return Client::count();
    }

    public function totalClinics(): int
    {
        return Clinic::count();
    }

    public function activeSubscriptions(): int
    {
        return Subscription::where('status', 'active')->count();
    }

    public function unpaidInvoices(): int
    {
        return Invoice::whereIn('status', ['unpaid', 'overdue'])->count();
    }

    public function monthlyRevenue(): float
    {
        return (float) Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
    }
}
