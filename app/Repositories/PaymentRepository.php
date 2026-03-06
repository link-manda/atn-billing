<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentRepository
{
    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return Payment::query()
            ->with(['invoice.clinic.client'])
            ->when($search, function ($q) use ($search) {
                $q->where('reference_number', 'ilike', "%{$search}%")
                  ->orWhereHas('invoice', fn ($i) => $i->where('invoice_number', 'ilike', "%{$search}%"));
            })
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Payment
    {
        return Payment::with(['invoice.clinic.client'])->findOrFail($id);
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function delete(int $id): void
    {
        Payment::destroy($id);
    }

    public function totalPaidForInvoice(int $invoice_id): float
    {
        return (float) Payment::where('invoice_id', $invoice_id)->sum('amount');
    }
}
