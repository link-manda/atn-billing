<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\ProductService;

class ProductList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editing_id = null;

    // Form fields
    public string $product_name = '';
    public string $product_code = '';
    public string $description = '';
    public string $base_price = '';
    public string $status = 'active';

    protected array $rules = [
        'product_name' => 'required|string|max:255',
        'product_code' => 'required|string|max:50',
        'description'  => 'nullable|string',
        'base_price'   => 'required|numeric|min:0',
        'status'       => 'required|in:active,inactive',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id, ProductService $service): void
    {
        $product = $service->find($id);
        $this->editing_id   = $id;
        $this->product_name = $product->product_name;
        $this->product_code = $product->product_code;
        $this->description  = $product->description ?? '';
        $this->base_price   = (string) $product->base_price;
        $this->status       = $product->status;
        $this->showModal = true;
    }

    public function save(ProductService $service): void
    {
        $this->validate();

        $data = [
            'product_name' => $this->product_name,
            'product_code' => $this->product_code,
            'description'  => $this->description ?: null,
            'base_price'   => $this->base_price,
            'status'       => $this->status,
        ];

        if ($this->editing_id) {
            $service->update($this->editing_id, $data);
            session()->flash('success', 'Product updated successfully.');
        } else {
            $service->create($data);
            session()->flash('success', 'Product created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id, ProductService $service): void
    {
        $service->delete($id);
        session()->flash('success', 'Product deleted.');
    }

    private function resetForm(): void
    {
        $this->editing_id = null;
        $this->product_name = $this->product_code = $this->description = $this->base_price = '';
        $this->status = 'active';
        $this->resetValidation();
    }

    public function render(ProductService $service)
    {
        return view('livewire.product.product-list', [
            'products' => $service->paginate(15, $this->search),
        ]);
    }
}
