<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientRepository
{
    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return Client::query()
            ->when($search, fn ($q) => $q->where('name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
                ->orWhere('company', 'ilike', "%{$search}%"))
            ->withCount('clinics')
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Client
    {
        return Client::findOrFail($id);
    }

    public function create(array $data): Client
    {
        return Client::create($data);
    }

    public function update(int $id, array $data): Client
    {
        $client = $this->find($id);
        $client->update($data);
        return $client;
    }

    public function delete(int $id): void
    {
        Client::destroy($id);
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Client::orderBy('name')->get();
    }
}
