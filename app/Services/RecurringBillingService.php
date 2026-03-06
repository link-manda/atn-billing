<?php

namespace App\Services;

use App\Repositories\InvoiceRepository;
use App\Repositories\SubscriptionRepository;
use Illuminate\Support\Carbon;

class RecurringBillingService
{
    public function __construct(
        private InvoiceRepository $invoiceRepository,
        private SubscriptionRepository $subscriptionRepository,
    ) {}

    /**
     * Process recurring billing for all active subscriptions.
     *
     * @return int Number of invoices generated
     */
    public function processRecurring(): int
    {
        $subscriptions = $this->subscriptionRepository->getActiveSubscriptions();
        $generated = 0;

        foreach ($subscriptions as $subscription) {
            if ($this->generateForSubscription($subscription)) {
                $generated++;
            }
        }

        return $generated;
    }

    /**
     * Attempt to generate an invoice for a single subscription for the current billing period.
     *
     * Monthly billing cycle: the billing period starts on the same day-of-month
     * as the subscription start_date, in the current month. If today's date
     * hasn't reached that day yet, we use the previous month.
     */
    private function generateForSubscription(\App\Models\Subscription $subscription): bool
    {
        $startDate   = Carbon::parse($subscription->start_date);
        $today       = Carbon::today();
        $billingDay  = $startDate->day;

        // Determine the billing period start for the current cycle
        $periodStart = Carbon::create($today->year, $today->month, min($billingDay, $today->daysInMonth));

        // If we haven't reached the billing day yet this month, use last month's period
        if ($today->lt($periodStart)) {
            $periodStart->subMonth();
            $periodStart->day = min($billingDay, $periodStart->daysInMonth);
        }

        $periodEnd = $periodStart->copy()->addMonth()->subDay();

        // Skip if an invoice already exists for this billing period (year + month of period start)
        $existing = $this->invoiceRepository->findExistingForBillingPeriod(
            $subscription->id,
            $periodStart->year,
            $periodStart->month
        );

        if ($existing) {
            return false;
        }

        // Generate a new invoice
        $amount   = (float) $subscription->price;
        $tax      = 0.0;
        $total    = $amount + $tax;
        $dueDate  = $periodStart->copy()->addDays(14)->toDateString();

        $invoiceNumber = $this->invoiceRepository->generateNextNumber();

        $data = [
            'invoice_number'  => $invoiceNumber,
            'subscription_id' => $subscription->id,
            'clinic_id'       => $subscription->clinic_id,
            'amount'          => $amount,
            'tax'             => $tax,
            'total'           => $total,
            'status'          => 'draft',
            'due_date'        => $dueDate,
        ];

        $this->invoiceRepository->create($data);

        return true;
    }
}
