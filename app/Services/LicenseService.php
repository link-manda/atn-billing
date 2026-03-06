<?php

namespace App\Services;

use App\Repositories\LicenseRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\ProductRepository;
use App\Models\LicenseKey;
use Illuminate\Pagination\LengthAwarePaginator;

class LicenseService
{
    public function __construct(
        private LicenseRepository $repository,
        private SubscriptionRepository $subscriptionRepository,
        private ProductRepository $productRepository
    ) {}

    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $search);
    }

    public function find(int $id): LicenseKey
    {
        return $this->repository->find($id);
    }

    public function generate(array $data): LicenseKey
    {
        $data['license_key'] = $this->repository->generateKey();
        $data['status']      = $data['status'] ?? 'active';
        return $this->repository->create($data);
    }

    public function suspend(int $id): LicenseKey
    {
        return $this->repository->changeStatus($id, 'suspended');
    }

    public function activate(int $id): LicenseKey
    {
        return $this->repository->changeStatus($id, 'active');
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    public function allSubscriptions(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->subscriptionRepository->getActiveSubscriptions();
    }

    public function allProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->productRepository->all();
    }
}
