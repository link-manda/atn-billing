<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Clients</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Manage all registered clients.</p>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">
            Add Client
        </flux:button>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    {{-- Search --}}
    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search clients..." />

    {{-- Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Name</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Company</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Email</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Phone</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Clinics</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($clients as $client)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/40 transition-colors">
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $client->name }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $client->company ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $client->email ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $client->phone ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <flux:badge variant="outline" size="sm">{{ $client->clinics_count }} clinics</flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button wire:click="openEdit({{ $client->id }})" size="sm" variant="ghost" icon="pencil-square" />
                                <flux:button wire:click="delete({{ $client->id }})"
                                    wire:confirm="Are you sure you want to delete this client?"
                                    size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-zinc-400">
                            <flux:icon.users class="w-8 h-8 mx-auto mb-2 opacity-40" />
                            No clients found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>{{ $clients->links() }}</div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="md:w-96 space-y-4">
        <flux:heading size="lg">{{ $editing_id ? 'Edit Client' : 'New Client' }}</flux:heading>

        <flux:field>
            <flux:label>Name <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
            <flux:input wire:model="name" placeholder="Client name" />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>Company</flux:label>
            <flux:input wire:model="company" placeholder="Company name" />
        </flux:field>

        <flux:field>
            <flux:label>Email</flux:label>
            <flux:input wire:model="email" type="email" placeholder="email@example.com" />
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>Phone</flux:label>
            <flux:input wire:model="phone" placeholder="+62..." />
        </flux:field>

        <flux:field>
            <flux:label>Address</flux:label>
            <flux:textarea wire:model="address" placeholder="Full address" rows="3" />
        </flux:field>

        <div class="flex justify-end gap-2">
            <flux:button wire:click="$set('showModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="save" variant="primary">{{ $editing_id ? 'Update' : 'Create' }}</flux:button>
        </div>
    </flux:modal>

</div>
