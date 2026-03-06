<?php

namespace App\Livewire\Payment;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\PaymentService;

class PaymentList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;

    // Form fields
    public ?int $invoice_id = null;
    public string $amount = '';
    public string $payment_method = 'bank_transfer';
    public string $payment_date = '';
    public string $reference_number = '';

    protected array $rules = [
        'invoice_id'       => 'required|exists:invoices,id',
        'amount'           => 'required|numeric|min:0.01',
        'payment_method'   => 'required|in:bank_transfer,cash,ewallet',
        'payment_date'     => 'required|date',
        'reference_number' => 'nullable|string|max:100',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->payment_date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function save(PaymentService $service): void
    {
        $this->validate();

        $service->recordPayment([
            'invoice_id'       => $this->invoice_id,
            'amount'           => $this->amount,
            'payment_method'   => $this->payment_method,
            'payment_date'     => $this->payment_date,
            'reference_number' => $this->reference_number ?: null,
        ]);

        session()->flash('success', 'Payment recorded. Invoice auto-status updated if fully paid.');
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id, PaymentService $service): void
    {
        $service->delete($id);
        session()->flash('success', 'Payment deleted.');
    }

    private function resetForm(): void
    {
        $this->invoice_id = null;
        $this->amount = $this->payment_date = $this->reference_number = '';
        $this->payment_method = 'bank_transfer';
        $this->resetValidation();
    }

    public function render(PaymentService $service)
    {
        return view('livewire.payment.payment-list', [
            'payments' => $service->paginate(15, $this->search),
            'invoices' => $service->allInvoices(),
        ]);
    }
}
