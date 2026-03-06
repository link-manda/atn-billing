<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\SubscriptionService;

class SubscriptionList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editing_id = null;

    // Form fields
    public ?int $clinic_id = null;
    public ?int $product_id = null;
    public string $price = '';
    public string $billing_cycle = 'monthly';
    public string $start_date = '';
    public string $end_date = '';
    public string $status = 'active';

    protected array $rules = [
        'clinic_id'     => 'required|exists:clinics,id',
        'product_id'    => 'required|exists:products,id',
        'price'         => 'required|numeric|min:0',
        'billing_cycle' => 'required|in:monthly,quarterly,yearly',
        'start_date'    => 'required|date',
        'end_date'      => 'nullable|date|after_or_equal:start_date',
        'status'        => 'required|in:active,inactive,expired,suspended',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->start_date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEdit(int $id, SubscriptionService $service): void
    {
        $sub = $service->find($id);
        $this->editing_id   = $id;
        $this->clinic_id    = $sub->clinic_id;
        $this->product_id   = $sub->product_id;
        $this->price        = (string) $sub->price;
        $this->billing_cycle= $sub->billing_cycle;
        $this->start_date   = $sub->start_date;
        $this->end_date     = $sub->end_date ?? '';
        $this->status       = $sub->status;
        $this->showModal = true;
    }

    public function save(SubscriptionService $service): void
    {
        $this->validate();

        $data = [
            'clinic_id'     => $this->clinic_id,
            'product_id'    => $this->product_id,
            'price'         => $this->price,
            'billing_cycle' => $this->billing_cycle,
            'start_date'    => $this->start_date,
            'end_date'      => $this->end_date ?: null,
            'status'        => $this->status,
        ];

        if ($this->editing_id) {
            $service->update($this->editing_id, $data);
            session()->flash('success', 'Subscription updated.');
        } else {
            $service->create($data);
            session()->flash('success', 'Subscription created.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function suspend(int $id, SubscriptionService $service): void
    {
        $service->suspend($id);
        session()->flash('success', 'Subscription suspended.');
    }

    public function activate(int $id, SubscriptionService $service): void
    {
        $service->activate($id);
        session()->flash('success', 'Subscription activated.');
    }

    public function delete(int $id, SubscriptionService $service): void
    {
        $service->delete($id);
        session()->flash('success', 'Subscription deleted.');
    }

    private function resetForm(): void
    {
        $this->editing_id = null;
        $this->clinic_id = $this->product_id = null;
        $this->price = $this->start_date = $this->end_date = '';
        $this->billing_cycle = 'monthly';
        $this->status = 'active';
        $this->resetValidation();
    }

    public function render(SubscriptionService $service)
    {
        return view('livewire.subscription.subscription-list', [
            'subscriptions' => $service->paginate(15, $this->search),
            'clinics'       => $service->allClinics(),
            'products'      => $service->allProducts(),
        ]);
    }
}
