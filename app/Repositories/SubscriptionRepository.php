<?php

namespace App\Repositories;

use App\Models\Subscription;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriptionRepository
{
    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return Subscription::query()
            ->with(['clinic.client', 'product'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('clinic', fn ($c) => $c->where('clinic_name', 'ilike', "%{$search}%"))
                  ->orWhereHas('product', fn ($p) => $p->where('product_name', 'ilike', "%{$search}%"));
            })
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Subscription
    {
        return Subscription::with(['clinic.client', 'product'])->findOrFail($id);
    }

    public function create(array $data): Subscription
    {
        return Subscription::create($data);
    }

    public function update(int $id, array $data): Subscription
    {
        $sub = Subscription::findOrFail($id);
        $sub->update($data);
        return $sub;
    }

    public function delete(int $id): void
    {
        Subscription::destroy($id);
    }

    public function getActiveSubscriptions(): \Illuminate\Database\Eloquent\Collection
    {
        return Subscription::where('status', 'active')->with(['clinic', 'product'])->get();
    }

    public function changeStatus(int $id, string $status): Subscription
    {
        $sub = Subscription::findOrFail($id);
        $sub->update(['status' => $status]);
        return $sub;
    }
}