<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\BankAccountService;

class BankAccountList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editing_id = null;

    // Form fields
    public string $bank_name = '';
    public string $account_name = '';
    public string $account_number = '';
    public string $branch = '';
    public string $swift_code = '';
    public bool $is_default = false;
    public bool $is_active = true;

    protected array $rules = [
        'bank_name' => 'required|string|max:100',
        'account_name' => 'required|string|max:255',
        'account_number' => 'required|string|max:50',
        'branch' => 'nullable|string|max:100',
        'swift_code' => 'nullable|string|max:20',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
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

    public function openEdit(int $id, BankAccountService $service): void
    {
        $bank = $service->find($id);
        $this->editing_id = $id;
        $this->bank_name = $bank->bank_name;
        $this->account_name = $bank->account_name;
        $this->account_number = $bank->account_number;
        $this->branch = $bank->branch ?? '';
        $this->swift_code = $bank->swift_code ?? '';
        $this->is_default = $bank->is_default;
        $this->is_active = $bank->is_active;

        $this->showModal = true;
    }

    public function save(BankAccountService $service): void
    {
        $this->validate();

        $data = [
            'bank_name' => $this->bank_name,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'branch' => $this->branch ?: null,
            'swift_code' => $this->swift_code ?: null,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ];

        if ($this->editing_id) {
            $service->update($this->editing_id, $data);
            session()->flash('success', 'Bank account updated successfully.');
        } else {
            $service->create($data);
            session()->flash('success', 'Bank account created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id, BankAccountService $service): void
    {
        try {
            $service->delete($id);
            session()->flash('success', 'Bank account deleted.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function makeDefault(int $id, BankAccountService $service): void
    {
        $service->makeDefault($id);
        session()->flash('success', 'Set as default bank account.');
    }

    public function toggleActive(int $id, BankAccountService $service): void
    {
        try {
            $service->toggleActive($id);
            session()->flash('success', 'Bank account status updated.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    private function resetForm(): void
    {
        $this->editing_id = null;
        $this->bank_name = $this->account_name = $this->account_number = $this->branch = $this->swift_code = '';
        $this->is_default = false;
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render(BankAccountService $service)
    {
        return view('livewire.settings.bank-account-list', [
            'bankAccounts' => $service->paginate(15, $this->search)
        ]);
    }
}
