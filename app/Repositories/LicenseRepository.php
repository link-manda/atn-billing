<?php

namespace App\Repositories;

use App\Models\LicenseKey;
use Illuminate\Pagination\LengthAwarePaginator;

class LicenseRepository
{
    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return LicenseKey::query()
            ->with(['clinic.client', 'subscription.product', 'product'])
            ->when($search, function ($q) use ($search) {
                $q->where('license_key', 'ilike', "%{$search}%")
                  ->orWhereHas('clinic', fn ($c) => $c->where('clinic_name', 'ilike', "%{$search}%"));
            })
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): LicenseKey
    {
        return LicenseKey::with(['clinic.client', 'subscription.product', 'product'])->findOrFail($id);
    }

    public function create(array $data): LicenseKey
    {
        return LicenseKey::create($data);
    }

    public function delete(int $id): void
    {
        LicenseKey::destroy($id);
    }

    public function changeStatus(int $id, string $status): LicenseKey
    {
        $license = LicenseKey::findOrFail($id);
        $license->update(['status' => $status]);
        return $license;
    }

    public function generateKey(): string
    {
        do {
            $key = 'ATN-SIPRIMA-' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4))
                . '-' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4));
        } while (LicenseKey::where('license_key', $key)->exists());

        return $key;
    }
}
