<?php

namespace App\Services;

use App\Repositories\ClinicRepository;
use App\Repositories\ClientRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ClinicService
{
    public function __construct(
        private ClinicRepository $repository,
        private ClientRepository $clientRepository
    ) {}

    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $search);
    }

    public function create(array $data): void
    {
        $this->repository->create($data);
    }

    public function update(int $id, array $data): void
    {
        $this->repository->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function allClients()
    {
        return $this->clientRepository->all();
    }
}
