<?php

namespace App\Services;

use App\Repositories\SubscriptionRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\ProductRepository;
use App\Models\Subscription;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriptionService
{
    public function __construct(
        private SubscriptionRepository $repository,
        private ClinicRepository $clinicRepository,
        private ProductRepository $productRepository
    ) {}

    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $search);
    }

    public function find(int $id): Subscription
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Subscription
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Subscription
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    public function suspend(int $id): Subscription
    {
        return $this->repository->changeStatus($id, 'suspended');
    }

    public function activate(int $id): Subscription
    {
        return $this->repository->changeStatus($id, 'active');
    }

    public function allClinics(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Clinic::with('client')->orderBy('clinic_name')->get();
    }

    public function allProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->productRepository->all();
    }
}
