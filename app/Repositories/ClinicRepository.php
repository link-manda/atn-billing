<?php

namespace App\Repositories;

use App\Models\Clinic;
use Illuminate\Pagination\LengthAwarePaginator;

class ClinicRepository
{
    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return Clinic::query()
            ->with('client')
            ->when($search, fn ($q) => $q->where('clinic_name', 'ilike', "%{$search}%")
                ->orWhere('city', 'ilike', "%{$search}%"))
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Clinic
    {
        return Clinic::with('client')->findOrFail($id);
    }

    public function create(array $data): Clinic
    {
        return Clinic::create($data);
    }

    public function update(int $id, array $data): Clinic
    {
        $clinic = Clinic::findOrFail($id);
        $clinic->update($data);
        return $clinic;
    }

    public function delete(int $id): void
    {
        Clinic::destroy($id);
    }
}
