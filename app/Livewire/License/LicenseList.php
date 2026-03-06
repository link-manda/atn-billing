<?php

namespace App\Livewire\License;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\LicenseService;

class LicenseList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editing_id = null;

    // Form fields
    public ?int $subscription_id = null;
    public ?int $product_id = null;
    public string $expired_at = '';
    public string $status = 'active';

    protected array $rules = [
        'subscription_id' => 'required|exists:subscriptions,id',
        'product_id'      => 'required|exists:products,id',
        'expired_at'      => 'nullable|date',
        'status'          => 'required|in:active,expired,suspended',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openGenerate(): void
    {
        $this->resetForm();
        $this->expired_at = now()->addYear()->format('Y-m-d');
        $this->showModal = true;
    }

    public function generate(LicenseService $service): void
    {
        $this->validate();

        // Get clinic_id from subscription
        $sub = $service->allSubscriptions()->firstWhere('id', $this->subscription_id);

        $service->generate([
            'subscription_id' => $this->subscription_id,
            'clinic_id'       => $sub?->clinic_id,
            'product_id'      => $this->product_id,
            'expired_at'      => $this->expired_at ?: null,
            'status'          => $this->status,
        ]);

        session()->flash('success', 'License key generated successfully.');
        $this->showModal = false;
        $this->resetForm();
    }

    public function suspend(int $id, LicenseService $service): void
    {
        $service->suspend($id);
        session()->flash('success', 'License suspended.');
    }

    public function activate(int $id, LicenseService $service): void
    {
        $service->activate($id);
        session()->flash('success', 'License activated.');
    }

    public function delete(int $id, LicenseService $service): void
    {
        $service->delete($id);
        session()->flash('success', 'License deleted.');
    }

    private function resetForm(): void
    {
        $this->editing_id = null;
        $this->subscription_id = $this->product_id = null;
        $this->expired_at = '';
        $this->status = 'active';
        $this->resetValidation();
    }

    public function render(LicenseService $service)
    {
        return view('livewire.license.license-list', [
            'licenses'      => $service->paginate(15, $this->search),
            'subscriptions' => $service->allSubscriptions(),
            'products'      => $service->allProducts(),
        ]);
    }
}
