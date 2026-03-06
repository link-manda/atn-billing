<?php

namespace App\Services;

use App\Repositories\ClientRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientService
{
    public function __construct(private ClientRepository $repository) {}

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

    public function all()
    {
        return $this->repository->all();
    }
}
