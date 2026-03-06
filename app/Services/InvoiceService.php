<?php

namespace App\Services;

use App\Repositories\InvoiceRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\ClinicRepository;
use App\Models\Invoice;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceService
{
    public function __construct(
        private InvoiceRepository $repository,
        private SubscriptionRepository $subscriptionRepository,
        private ClinicRepository $clinicRepository
    ) {}

    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $search);
    }

    public function find(int $id): Invoice
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Invoice
    {
        $data['invoice_number'] = $this->repository->generateNextNumber();

        // Calculate total
        $amount = (float) ($data['amount'] ?? 0);
        $tax    = (float) ($data['tax'] ?? 0);
        $data['total'] = $amount + $tax;

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Invoice
    {
        $amount = (float) ($data['amount'] ?? 0);
        $tax    = (float) ($data['tax'] ?? 0);
        $data['total'] = $amount + $tax;

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    public function markPaid(int $id): Invoice
    {
        return $this->repository->markStatus($id, 'paid', now()->toDateTimeString());
    }

    public function markOverdue(int $id): Invoice
    {
        return $this->repository->markStatus($id, 'overdue');
    }

    public function markSent(int $id): Invoice
    {
        return $this->repository->markStatus($id, 'sent');
    }

    public function markCancelled(int $id): Invoice
    {
        return $this->repository->markStatus($id, 'cancelled');
    }

    public function allSubscriptions(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->subscriptionRepository->getActiveSubscriptions();
    }

    public function allClinics(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Clinic::with('client')->orderBy('clinic_name')->get();
    }
}