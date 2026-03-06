<?php

namespace App\Services;

use App\Models\BankAccount;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BankAccountService
{
    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        $query = BankAccount::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function allActive()
    {
        return BankAccount::where('is_active', true)->get();
    }

    public function find(int $id): BankAccount
    {
        return BankAccount::findOrFail($id);
    }

    public function create(array $data): BankAccount
    {
        return DB::transaction(function () use ($data) {
            if (!empty($data['is_default'])) {
                $this->unsetDefault();
            }

            return BankAccount::create($data);
        });
    }

    public function update(int $id, array $data): BankAccount
    {
        return DB::transaction(function () use ($id, $data) {
            if (!empty($data['is_default'])) {
                $this->unsetDefault($id);
            }

            $bankAccount = $this->find($id);
            $bankAccount->update($data);

            return $bankAccount;
        });
    }

    public function delete(int $id): void
    {
        $bankAccount = $this->find($id);

        // Prevent deleting if it's the last active default account
        if ($bankAccount->is_default && $bankAccount->is_active) {
            $activeCount = BankAccount::where('is_active', true)->count();
            if ($activeCount <= 1) {
                throw new \Exception("Cannot delete the only active default bank account.");
            }
        }

        $bankAccount->delete();
    }

    public function toggleActive(int $id): void
    {
        $bankAccount = $this->find($id);

        if ($bankAccount->is_active && $bankAccount->is_default) {
            $activeCount = BankAccount::where('is_active', true)->count();
            if ($activeCount <= 1) {
                throw new \Exception("Cannot deactivate the only active default bank account.");
            }
        }

        $bankAccount->update(['is_active' => !$bankAccount->is_active]);
    }

    public function makeDefault(int $id): void
    {
        DB::transaction(function () use ($id) {
            $this->unsetDefault();
            $bankAccount = $this->find($id);
            $bankAccount->update([
                'is_default' => true,
                'is_active' => true // A default account should be active
            ]);
        });
    }

    private function unsetDefault(?int $excludeId = null): void
    {
        $query = BankAccount::where('is_default', true);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        $query->update(['is_default' => false]);
    }
}
