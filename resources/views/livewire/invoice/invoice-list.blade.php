<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Invoices</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Manage and track all invoices.</p>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">Generate Invoice</flux:button>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by invoice number, clinic, or client..." />

    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Invoice #</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Clinic</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Client</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Amount</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Tax</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Total</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Due Date</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($invoices as $invoice)
                    @php
                        $statusColors = ['draft'=>'zinc','sent'=>'blue','paid'=>'green','overdue'=>'red','cancelled'=>'stone'];
                    @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/40 transition-colors">
                        <td class="px-4 py-3">
                            <flux:badge variant="outline" size="sm">{{ $invoice->invoice_number }}</flux:badge>
                        </td>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $invoice->clinic?->clinic_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs">{{ $invoice->clinic?->client?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">Rp {{ number_format($invoice->tax ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $invoice->due_date }}</td>
                        <td class="px-4 py-3">
                            <flux:badge color="{{ $statusColors[$invoice->status] ?? 'zinc' }}" size="sm">{{ ucfirst($invoice->status) }}</flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('invoices.show', $invoice->id) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-zinc-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                   title="View Invoice">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if($invoice->status === 'draft')
                                    <flux:button wire:click="markSent({{ $invoice->id }})" size="sm" variant="ghost" icon="paper-airplane" class="text-blue-500 hover:text-blue-600" />
                                @endif
                                @if(in_array($invoice->status, ['sent','overdue','draft']))
                                    <flux:button wire:click="markPaid({{ $invoice->id }})"
                                        wire:confirm="Mark invoice as paid?"
                                        size="sm" variant="ghost" icon="check-circle" class="text-green-600 hover:text-green-700" />
                                    <flux:button wire:click="markOverdue({{ $invoice->id }})"
                                        wire:confirm="Mark invoice as overdue?"
                                        size="sm" variant="ghost" icon="exclamation-circle" class="text-red-500 hover:text-red-600" />
                                @endif
                                <flux:button wire:click="openEdit({{ $invoice->id }})" size="sm" variant="ghost" icon="pencil-square" />
                                <flux:button wire:click="delete({{ $invoice->id }})"
                                    wire:confirm="Delete this invoice?"
                                    size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-zinc-400">
                            <flux:icon.document-text class="w-8 h-8 mx-auto mb-2 opacity-40" />
                            No invoices found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $invoices->links() }}</div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="md:w-[28rem] space-y-4">
        <flux:heading size="lg">{{ $editing_id ? 'Edit Invoice' : 'Generate Invoice' }}</flux:heading>

        <form wire:submit="save" class="space-y-4">
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
                <flux:label>Clinic <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <select wire:model="clinic_id"
                    class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                    <option value="">Select clinic...</option>
                    @foreach($clinics as $clinic)
                        <option value="{{ $clinic->id }}">{{ $clinic->clinic_name }}</option>
                    @endforeach
                </select>
                <flux:error name="clinic_id" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Amount (Rp)</flux:label>
                    <flux:input wire:model="amount" type="number" placeholder="0" />
                    <flux:error name="amount" />
                </flux:field>
                <flux:field>
                    <flux:label>Tax (Rp)</flux:label>
                    <flux:input wire:model="tax" type="number" placeholder="0" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Due Date</flux:label>
                <flux:input wire:model="due_date" type="date" />
                <flux:error name="due_date" />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <select wire:model="status"
                    class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)" variant="ghost" type="button">Cancel</flux:button>
                <flux:button variant="primary" type="submit">{{ $editing_id ? 'Update' : 'Generate' }}</flux:button>
            </div>
        </form>
    </flux:modal>

</div>
