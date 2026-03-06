<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceRepository
{
    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return Invoice::query()
            ->with(['clinic.client', 'subscription.product'])
            ->when($search, function ($q) use ($search) {
                $q->where('invoice_number', 'ilike', "%{$search}%")
                  ->orWhereHas('clinic', fn ($c) => $c->where('clinic_name', 'ilike', "%{$search}%"))
                  ->orWhereHas('clinic.client', fn ($c) => $c->where('name', 'ilike', "%{$search}%"));
            })
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Invoice
    {
        return Invoice::with(['clinic.client', 'subscription.product', 'payments'])->findOrFail($id);
    }

    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    public function update(int $id, array $data): Invoice
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($data);
        return $invoice;
    }

    public function delete(int $id): void
    {
        Invoice::destroy($id);
    }

    public function findByMonth(int $subscription_id, int $month): ?Invoice
    {
        return Invoice::where('subscription_id', $subscription_id)
            ->whereMonth('created_at', $month)
            ->first();
    }

    public function findExistingForBillingPeriod(int $subscription_id, int $year, int $month): ?Invoice
    {
        return Invoice::where('subscription_id', $subscription_id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->first();
    }

    public function updatePdfPath(int $id, string $path): Invoice
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['pdf_path' => $path]);
        return $invoice;
    }

    public function generateNextNumber(): string
    {
        $year = now()->year;
        $last = Invoice::whereYear('created_at', $year)->count();
        return sprintf('INV-%d-%04d', $year, $last + 1);
    }

    public function markStatus(int $id, string $status, ?string $paid_at = null): Invoice
    {
        $invoice = Invoice::findOrFail($id);
        $data = ['status' => $status];
        if ($paid_at) {
            $data['paid_at'] = $paid_at;
        }
        $invoice->update($data);
        return $invoice;
    }
}