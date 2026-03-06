<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'billing:generate-invoices';

    public function handle(
        SubscriptionRepository $subscription_repo,
        InvoiceService $invoice_service
    ) {

        $subscriptions = $subscription_repo->getActiveSubscriptions();

        foreach ($subscriptions as $subscription) {

            $invoice_service->generateMonthlyInvoice($subscription);

        }

        $this->info('Monthly invoices generated.');
    }
}
