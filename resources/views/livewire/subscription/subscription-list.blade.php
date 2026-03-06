<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Subscriptions</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Manage clinic product subscriptions.</p>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">Add Subscription</flux:button>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by clinic or product..." />

    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Clinic</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Client</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Product</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Price</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Cycle</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Start Date</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($subscriptions as $sub)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/40 transition-colors">
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $sub->clinic?->clinic_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs">{{ $sub->clinic?->client?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $sub->product?->product_name ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">Rp {{ number_format($sub->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <flux:badge variant="outline" size="sm">{{ ucfirst($sub->billing_cycle) }}</flux:badge>
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $sub->start_date }}</td>
                        <td class="px-4 py-3">
                            @php $statusColors = ['active'=>'green','inactive'=>'zinc','expired'=>'red','suspended'=>'amber']; @endphp
                            <flux:badge color="{{ $statusColors[$sub->status] ?? 'zinc' }}" size="sm">{{ ucfirst($sub->status) }}</flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button wire:click="openEdit({{ $sub->id }})" size="sm" variant="ghost" icon="pencil-square" />
                                @if($sub->status === 'active')
                                    <flux:button wire:click="suspend({{ $sub->id }})"
                                        wire:confirm="Suspend this subscription?"
                                        size="sm" variant="ghost" icon="pause-circle" class="text-amber-500 hover:text-amber-600" />
                                @else
                                    <flux:button wire:click="activate({{ $sub->id }})"
                                        wire:confirm="Activate this subscription?"
                                        size="sm" variant="ghost" icon="play-circle" class="text-green-600 hover:text-green-700" />
                                @endif
                                <flux:button wire:click="delete({{ $sub->id }})"
                                    wire:confirm="Delete this subscription?"
                                    size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-zinc-400">
                            <flux:icon.arrow-path class="w-8 h-8 mx-auto mb-2 opacity-40" />
                            No subscriptions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $subscriptions->links() }}</div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="md:w-[28rem] space-y-4">
        <flux:heading size="lg">{{ $editing_id ? 'Edit Subscription' : 'New Subscription' }}</flux:heading>

        <form wire:submit="save" class="space-y-4">
            <flux:field>
                <flux:label>Clinic <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <select wire:model="clinic_id"
                    class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                    <option value="">Select clinic...</option>
                    @foreach($clinics as $clinic)
                        <option value="{{ $clinic->id }}">{{ $clinic->clinic_name }} ({{ $clinic->client?->name }})</option>
                    @endforeach
                </select>
                <flux:error name="clinic_id" />
            </flux:field>

            <flux:field>
                <flux:label>Product <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <select wire:model="product_id"
                    class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                    <option value="">Select product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_name }} (Rp {{ number_format($product->base_price, 0, ',', '.') }})</option>
                    @endforeach
                </select>
                <flux:error name="product_id" />
            </flux:field>

            <flux:field>
                <flux:label>Custom Price (Rp) <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <flux:input wire:model="price" type="text" inputmode="decimal" placeholder="0,00" />
                <flux:error name="price" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Billing Cycle</flux:label>
                    <select wire:model="billing_cycle"
                        class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </flux:field>
                <flux:field>
                    <flux:label>Status</flux:label>
                    <select wire:model="status"
                        class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="expired">Expired</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </flux:field>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Start Date</flux:label>
                    <flux:input wire:model="start_date" type="date" />
                    <flux:error name="start_date" />
                </flux:field>
                <flux:field>
                    <flux:label>End Date</flux:label>
                    <flux:input wire:model="end_date" type="date" />
                    <flux:error name="end_date" />
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)" variant="ghost" type="button">Cancel</flux:button>
                <flux:button variant="primary" type="submit">{{ $editing_id ? 'Update' : 'Create' }}</flux:button>
            </div>
        </form>
    </flux:modal>

</div>
