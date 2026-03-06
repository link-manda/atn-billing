<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return Product::query()
            ->when($search, fn ($q) => $q->where('product_name', 'ilike', "%{$search}%")
                ->orWhere('product_code', 'ilike', "%{$search}%"))
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->find($id);
        $product->update($data);
        return $product;
    }

    public function delete(int $id): void
    {
        Product::destroy($id);
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('status', 'active')->orderBy('product_name')->get();
    }
}
