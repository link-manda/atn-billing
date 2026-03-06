<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Repositories\InvoiceRepository;
use App\Models\Payment;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentService
{
    public function __construct(
        private PaymentRepository $repository,
        private InvoiceRepository $invoiceRepository
    ) {}

    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $search);
    }

    public function find(int $id): Payment
    {
        return $this->repository->find($id);
    }

    public function recordPayment(array $data): Payment
    {
        $payment = $this->repository->create($data);

        // Business rule: auto-mark invoice as paid when total payment equals invoice total
        $invoice   = $this->invoiceRepository->find($data['invoice_id']);
        $totalPaid = $this->repository->totalPaidForInvoice($data['invoice_id']);

        if ($totalPaid >= (float) $invoice->total) {
            $this->invoiceRepository->markStatus($data['invoice_id'], 'paid', now()->toDateTimeString());
        }

        return $payment;
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    public function allInvoices(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Invoice::with('clinic')
            ->whereIn('status', ['sent', 'unpaid', 'overdue', 'draft'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
