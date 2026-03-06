<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Clinics</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Manage all clinic locations.</p>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">
            Add Clinic
        </flux:button>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    {{-- Search --}}
    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search clinics..." />

    {{-- Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Clinic Name</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Client</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">City</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($clinics as $clinic)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/40 transition-colors">
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $clinic->clinic_name }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $clinic->client?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $clinic->city ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($clinic->status === 'active')
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Inactive</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button wire:click="openEdit({{ $clinic->id }})" size="sm" variant="ghost" icon="pencil-square" />
                                <flux:button wire:click="delete({{ $clinic->id }})"
                                    wire:confirm="Are you sure you want to delete this clinic?"
                                    size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-zinc-400">
                            No clinics found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>{{ $clinics->links() }}</div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="md:w-96 space-y-4">
        <flux:heading size="lg">{{ $editing_id ? 'Edit Clinic' : 'New Clinic' }}</flux:heading>

        <form wire:submit="save" class="space-y-4">
            <flux:field>
                <flux:label>Client <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <select wire:model="client_id"
                    class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                    <option value="">Select client...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
                <flux:error name="client_id" />
            </flux:field>

            <flux:field>
                <flux:label>Clinic Name <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <flux:input wire:model="clinic_name" placeholder="Clinic name" />
                <flux:error name="clinic_name" />
            </flux:field>

            <flux:field>
                <flux:label>City</flux:label>
                <flux:input wire:model="city" placeholder="City" />
            </flux:field>

            <flux:field>
                <flux:label>Address</flux:label>
                <flux:textarea wire:model="clinic_address" placeholder="Clinic address" rows="2" />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <select wire:model="status"
                    class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showModal', false)" variant="ghost" type="button">Cancel</flux:button>
                <flux:button variant="primary" type="submit">{{ $editing_id ? 'Update' : 'Create' }}</flux:button>
            </div>
        </form>
    </flux:modal>

</div>
