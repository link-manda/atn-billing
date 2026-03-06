<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">License Keys</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Manage software license keys per subscription.</p>
        </div>
        <flux:button wire:click="openGenerate" variant="primary" icon="key">Generate License</flux:button>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by license key or clinic..." />

    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">License Key</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Clinic</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Client</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Product</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Expires</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($licenses as $license)
                    @php $statusColors = ['active'=>'green','expired'=>'red','suspended'=>'amber']; @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/40 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs bg-zinc-100 dark:bg-zinc-700 px-2 py-1 rounded text-zinc-800 dark:text-zinc-200 font-semibold">
                                {{ $license->license_key }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $license->clinic?->clinic_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs">{{ $license->clinic?->client?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                            {{ $license->product?->product_name ?? $license->subscription?->product?->product_name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                            @if($license->expired_at)
                                <span class="{{ strtotime($license->expired_at) < time() ? 'text-red-500' : '' }}">
                                    {{ $license->expired_at }}
                                </span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <flux:badge color="{{ $statusColors[$license->status] ?? 'zinc' }}" size="sm">{{ ucfirst($license->status) }}</flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($license->status === 'active')
                                    <flux:button wire:click="suspend({{ $license->id }})"
                                        wire:confirm="Suspend this license?"
                                        size="sm" variant="ghost" icon="pause-circle" class="text-amber-500 hover:text-amber-600" />
                                @else
                                    <flux:button wire:click="activate({{ $license->id }})"
                                        wire:confirm="Activate this license?"
                                        size="sm" variant="ghost" icon="play-circle" class="text-green-600 hover:text-green-700" />
                                @endif
                                <flux:button wire:click="delete({{ $license->id }})"
                                    wire:confirm="Delete this license key?"
                                    size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-zinc-400">
                            <flux:icon.key class="w-8 h-8 mx-auto mb-2 opacity-40" />
                            No license keys generated yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $licenses->links() }}</div>

    {{-- Generate License Modal --}}
    <flux:modal wire:model="showModal" class="md:w-96 space-y-4">
        <flux:heading size="lg">Generate License Key</flux:heading>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">A unique license key in format <code class="font-mono bg-zinc-100 dark:bg-zinc-700 px-1 rounded">ATN-SIPRIMA-XXXX-XXXX</code> will be auto-generated.</p>

        <form wire:submit="generate" class="space-y-4">
            <flux:field>
                <flux:label>Subscription <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <select wire:model="subscription_id"
                    class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                    <option value="">Select subscription...</option>
                    @foreach($subscriptions as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->clinic?->clinic_name ?? 'N/A' }} — {{ $sub->product?->product_name ?? 'N/A' }}</option>
                    @endforeach
                </select>
                <flux:error name="subscription_id" />
            </flux:field>

            <flux:field>
                <flux:label>Product <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <select wire:model="product_id"
                    class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                    <option value="">Select product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
                <flux:error name="product_id" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Expires On</flux:label>
                    <flux:input wire:model="expired_at" type="date" />
                </flux:field>
                <flux:field>
                    <flux:label>Status</flux:label>
                    <select wire:model="status"
                        class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="expired">Expired</option>
                    </select>
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)" variant="ghost" type="button">Cancel</flux:button>
                <flux:button variant="primary" type="submit" icon="key">Generate</flux:button>
            </div>
        </form>
    </flux:modal>

</div>
