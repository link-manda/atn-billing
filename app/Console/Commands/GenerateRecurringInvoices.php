<?php

namespace App\Console\Commands;

use App\Services\RecurringBillingService;
use Illuminate\Console\Command;

class GenerateRecurringInvoices extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'billing:generate-invoices';

    /**
     * The console command description.
     */
    protected $description = 'Generate recurring invoices for all active subscriptions that are due for a new billing cycle.';

    /**
     * Execute the console command.
     */
    public function handle(RecurringBillingService $billingService): int
    {
        $this->info('Starting recurring invoice generation...');

        $count = $billingService->processRecurring();

        if ($count > 0) {
            $this->info("✓ Generated {$count} invoice(s) successfully.");
        } else {
            $this->line("  No new invoices needed at this time.");
        }

        return Command::SUCCESS;
    }
}
