<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\ClientService;

class ClientList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editing_id = null;

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $address = '';

    protected array $rules = [
        'name'    => 'required|string|max:255',
        'email'   => 'nullable|email|max:255',
        'phone'   => 'nullable|string|max:50',
        'company' => 'nullable|string|max:255',
        'address' => 'nullable|string',
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

    public function openEdit(int $id, ClientService $service): void
    {
        $client = $service->find($id);
        $this->editing_id = $id;
        $this->name    = $client->name;
        $this->email   = $client->email ?? '';
        $this->phone   = $client->phone ?? '';
        $this->company = $client->company ?? '';
        $this->address = $client->address ?? '';
        $this->showModal = true;
    }

    public function save(ClientService $service): void
    {
        $this->validate();

        $data = [
            'name'    => $this->name,
            'email'   => $this->email ?: null,
            'phone'   => $this->phone ?: null,
            'company' => $this->company ?: null,
            'address' => $this->address ?: null,
        ];

        if ($this->editing_id) {
            $service->update($this->editing_id, $data);
            session()->flash('success', 'Client updated successfully.');
        } else {
            $service->create($data);
            session()->flash('success', 'Client created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id, ClientService $service): void
    {
        $service->delete($id);
        session()->flash('success', 'Client deleted.');
    }

    private function resetForm(): void
    {
        $this->editing_id = null;
        $this->name = $this->email = $this->phone = $this->company = $this->address = '';
        $this->resetValidation();
    }

    public function render(ClientService $service)
    {
        return view('livewire.client.client-list', [
            'clients' => $service->paginate(15, $this->search),
        ]);
    }
}
