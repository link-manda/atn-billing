<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Bank Accounts</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Manage bank accounts for invoice payments.</p>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">Add Bank Account</flux:button>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    @if(session('error'))
        <flux:callout variant="danger" icon="exclamation-circle">{{ session('error') }}</flux:callout>
    @endif

    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by bank name, account name, or account number..." />

    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Bank Name</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Account Name</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Account Number</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Branch</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Default</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($bankAccounts as $bank)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/40 transition-colors">
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $bank->bank_name }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $bank->account_name }}</td>
                        <td class="px-4 py-3">
                            <flux:badge variant="outline" size="sm">{{ $bank->account_number }}</flux:badge>
                        </td>
                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $bank->branch ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <button type="button" wire:click="toggleActive({{ $bank->id }})" class="focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-full">
                                @if($bank->is_active)
                                    <flux:badge color="green" size="sm" class="cursor-pointer">Active</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" class="cursor-pointer">Inactive</flux:badge>
                                @endif
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            @if($bank->is_default)
                                <flux:badge color="blue" size="sm" icon="star">Default</flux:badge>
                            @else
                                <button type="button" wire:click="makeDefault({{ $bank->id }})" class="text-blue-500 hover:text-blue-700 text-xs px-2 py-1 bg-blue-50 hover:bg-blue-100 rounded transition-colors">
                                    Set Default
                                </button>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button wire:click="openEdit({{ $bank->id }})" size="sm" variant="ghost" icon="pencil-square" title="Edit" />
                                <flux:button wire:click="delete({{ $bank->id }})"
                                    wire:confirm="Are you sure you want to delete this bank account?"
                                    size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" title="Delete" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-zinc-400">
                            <flux:icon.building-library class="w-8 h-8 mx-auto mb-2 opacity-40" />
                            No bank accounts found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $bankAccounts->links() }}</div>

    {{-- Modal Form --}}
    <flux:modal wire:model="showModal" class="md:w-[32rem] space-y-4">
        <flux:heading size="lg">{{ $editing_id ? 'Edit Bank Account' : 'New Bank Account' }}</flux:heading>

        <form wire:submit="save" class="space-y-4">
            <flux:field>
                <flux:label>Bank Name <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <flux:input wire:model="bank_name" placeholder="e.g. Bank Central Asia (BCA)" />
                <flux:error name="bank_name" />
            </flux:field>

            <flux:field>
                <flux:label>Account Name <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <flux:input wire:model="account_name" placeholder="e.g. PT Aplikasi Teknologi Nusantara" />
                <flux:error name="account_name" />
            </flux:field>

            <flux:field>
                <flux:label>Account Number <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <flux:input wire:model="account_number" placeholder="e.g. 1234567890" />
                <flux:error name="account_number" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Branch (Optional)</flux:label>
                    <flux:input wire:model="branch" placeholder="e.g. KCP Sudirman" />
                    <flux:error name="branch" />
                </flux:field>

                <flux:field>
                    <flux:label>SWIFT/BIC Code (Optional)</flux:label>
                    <flux:input wire:model="swift_code" placeholder="e.g. CENAIDJA" />
                    <flux:error name="swift_code" />
                </flux:field>
            </div>

            <div class="flex items-center justify-between p-3 border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-zinc-900 dark:text-white">Active Status</span>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Can this account be used for invoices?</span>
                </div>
                <!-- Checkbox for Active -->
                <div class="flex items-center">
                    <input type="checkbox" wire:model="is_active" class="w-4 h-4 text-primary-600 rounded" />
                </div>
            </div>

            <div class="flex items-center justify-between p-3 border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-zinc-900 dark:text-white">Set as Default</span>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Use this account automatically for new invoices</span>
                </div>
                <!-- Checkbox for Default -->
                <div class="flex items-center">
                    <input type="checkbox" wire:model="is_default" class="w-4 h-4 text-primary-600 rounded" />
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)" variant="ghost" type="button">Cancel</flux:button>
                <flux:button variant="primary" type="submit">{{ $editing_id ? 'Update' : 'Create' }}</flux:button>
            </div>
        </form>
    </flux:modal>

</div>
