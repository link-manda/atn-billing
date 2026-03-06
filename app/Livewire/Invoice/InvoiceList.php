<?php

namespace App\Livewire\Invoice;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\InvoiceService;

class InvoiceList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editing_id = null;

    // Form fields
    public ?int $subscription_id = null;
    public ?int $clinic_id = null;
    public string $amount = '';
    public string $tax = '0';
    public string $due_date = '';
    public string $status = 'draft';

    protected array $rules = [
        'subscription_id' => 'required|exists:subscriptions,id',
        'clinic_id'       => 'required|exists:clinics,id',
        'amount'          => 'required|numeric|min:0',
        'tax'             => 'numeric|min:0',
        'due_date'        => 'required|date',
        'status'          => 'required|in:draft,sent,paid,overdue,cancelled',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->due_date = now()->addDays(7)->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEdit(int $id, InvoiceService $service): void
    {
        $inv = $service->find($id);
        $this->editing_id      = $id;
        $this->subscription_id = $inv->subscription_id;
        $this->clinic_id       = $inv->clinic_id;

        // Format amount back to Indonesian locale
        $amountStr = number_format($inv->amount, 2, ',', '.');
        if (str_ends_with($amountStr, ',00')) {
            $amountStr = substr($amountStr, 0, -3);
        }
        $this->amount          = $amountStr;

        // Format tax back to Indonesian locale
        $taxStr = number_format($inv->tax, 2, ',', '.');
        if (str_ends_with($taxStr, ',00')) {
            $taxStr = substr($taxStr, 0, -3);
        }
        $this->tax             = $taxStr;

        $this->due_date        = $inv->due_date;
        $this->status          = $inv->status;
        $this->showModal = true;
    }

    public function save(InvoiceService $service): void
    {
        // Sanitize amount and tax: '799.990,30' -> '799990.30'
        $this->amount = str_replace(['.', ','], ['', '.'], $this->amount);
        $this->tax = str_replace(['.', ','], ['', '.'], $this->tax);

        $this->validate();

        $data = [
            'subscription_id' => $this->subscription_id,
            'clinic_id'       => $this->clinic_id,
            'amount'          => $this->amount,
            'tax'             => $this->tax,
            'due_date'        => $this->due_date,
            'status'          => $this->status,
        ];

        if ($this->editing_id) {
            $service->update($this->editing_id, $data);
            session()->flash('success', 'Invoice updated.');
        } else {
            $service->create($data);
            session()->flash('success', 'Invoice generated successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function markPaid(int $id, InvoiceService $service): void
    {
        $service->markPaid($id);
        session()->flash('success', 'Invoice marked as paid.');
    }

    public function markOverdue(int $id, InvoiceService $service): void
    {
        $service->markOverdue($id);
        session()->flash('success', 'Invoice marked as overdue.');
    }

    public function markSent(int $id, InvoiceService $service): void
    {
        $service->markSent($id);
        session()->flash('success', 'Invoice sent.');
    }

    public function delete(int $id, InvoiceService $service): void
    {
        $service->delete($id);
        session()->flash('success', 'Invoice deleted.');
    }

    private function resetForm(): void
    {
        $this->editing_id = null;
        $this->subscription_id = $this->clinic_id = null;
        $this->amount = $this->due_date = '';
        $this->tax = '0';
        $this->status = 'draft';
        $this->resetValidation();
    }

    public function render(InvoiceService $service)
    {
        return view('livewire.invoice.invoice-list', [
            'invoices'      => $service->paginate(15, $this->search),
            'subscriptions' => $service->allSubscriptions(),
            'clinics'       => $service->allClinics(),
        ]);
    }
}
