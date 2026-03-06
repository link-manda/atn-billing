<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Invoice;

class RecentInvoices extends Component
{
    public function render()
    {
        // Get 5 most recent invoices
        $recentInvoices = Invoice::with(['clinic.client'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.dashboard.recent-invoices', [
            'recentInvoices' => $recentInvoices
        ]);
    }
}
