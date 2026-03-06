<?php

namespace App\Livewire\Clinic;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\ClinicService;

class ClinicList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editing_id = null;

    // Form fields — $client_id must be untyped to accept string from HTML select
    public $client_id = null;
    public string $clinic_name = '';
    public string $clinic_address = '';
    public string $city = '';
    public string $status = 'active';

    protected array $rules = [
        'client_id'     => 'required|exists:clients,id',
        'clinic_name'   => 'required|string|max:255',
        'clinic_address'=> 'nullable|string',
        'city'          => 'nullable|string|max:100',
        'status'        => 'required|in:active,inactive',
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

    public function openEdit(int $id, ClinicService $service): void
    {
        $clinic = $service->find($id);
        $this->editing_id    = $id;
        $this->client_id     = (string) $clinic->client_id;
        $this->clinic_name   = $clinic->clinic_name;
        $this->clinic_address= $clinic->clinic_address ?? '';
        $this->city          = $clinic->city ?? '';
        $this->status        = $clinic->status;
        $this->showModal = true;
    }

    public function save(ClinicService $service): void
    {
        $this->validate();

        $data = [
            'client_id'      => (int) $this->client_id,
            'clinic_name'    => $this->clinic_name,
            'clinic_address' => $this->clinic_address ?: null,
            'city'           => $this->city ?: null,
            'status'         => $this->status,
        ];

        if ($this->editing_id) {
            $service->update($this->editing_id, $data);
            session()->flash('success', 'Clinic updated successfully.');
        } else {
            $service->create($data);
            session()->flash('success', 'Clinic created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id, ClinicService $service): void
    {
        $service->delete($id);
        session()->flash('success', 'Clinic deleted.');
    }

    private function resetForm(): void
    {
        $this->editing_id = null;
        $this->client_id = null;
        $this->clinic_name = $this->clinic_address = $this->city = '';
        $this->status = 'active';
        $this->resetValidation();
    }

    public function render(ClinicService $service)
    {
        return view('livewire.clinic.clinic-list', [
            'clinics' => $service->paginate(15, $this->search),
            'clients' => $service->allClients(),
        ]);
    }
}
