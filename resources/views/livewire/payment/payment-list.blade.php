<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Payments</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Record and track all payment transactions.</p>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">Record Payment</flux:button>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by invoice number or reference..." />

    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Invoice #</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Client</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Amount</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Method</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Date</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Reference #</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($payments as $payment)
                    @php
                        $methodColors = ['bank_transfer'=>'blue','cash'=>'green','ewallet'=>'purple'];
                        $methodLabels = ['bank_transfer'=>'Bank Transfer','cash'=>'Cash','ewallet'=>'E-Wallet'];
                    @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/40 transition-colors">
                        <td class="px-4 py-3">
                            <flux:badge variant="outline" size="sm">{{ $payment->invoice?->invoice_number ?? '—' }}</flux:badge>
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $payment->invoice?->clinic?->client?->name ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <flux:badge color="{{ $methodColors[$payment->payment_method] ?? 'zinc' }}" size="sm">
                                {{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $payment->payment_date }}</td>
                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs font-mono">{{ $payment->reference_number ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <flux:button wire:click="delete({{ $payment->id }})"
                                wire:confirm="Delete this payment record?"
                                size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-zinc-400">
                            <flux:icon.credit-card class="w-8 h-8 mx-auto mb-2 opacity-40" />
                            No payments recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $payments->links() }}</div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="md:w-96 space-y-4">
        <flux:heading size="lg">Record Payment</flux:heading>

        <form wire:submit="save" class="space-y-4">
            <flux:field>
                <flux:label>Invoice <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <select wire:model="invoice_id"
                    class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                    <option value="">Select invoice...</option>
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}">{{ $inv->invoice_number }} — {{ $inv->clinic?->clinic_name ?? 'N/A' }} (Rp {{ number_format($inv->total, 0, ',', '.') }})</option>
                    @endforeach
                </select>
                <flux:error name="invoice_id" />
            </flux:field>

            <flux:field>
                <flux:label>Amount (Rp) <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <flux:input wire:model="amount" type="number" placeholder="0" />
                <flux:error name="amount" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Payment Method</flux:label>
                    <select wire:model="payment_method"
                        class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="ewallet">E-Wallet</option>
                    </select>
                </flux:field>
                <flux:field>
                    <flux:label>Payment Date</flux:label>
                    <flux:input wire:model="payment_date" type="date" />
                    <flux:error name="payment_date" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Reference Number</flux:label>
                <flux:input wire:model="reference_number" placeholder="e.g. TRF-20260305-001" />
            </flux:field>

            <flux:callout variant="info" icon="information-circle" class="text-xs">
                Invoice will be automatically marked as <strong>Paid</strong> when total payment equals invoice total.
            </flux:callout>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)" variant="ghost" type="button">Cancel</flux:button>
                <flux:button variant="primary" type="submit">Record Payment</flux:button>
            </div>
        </form>
    </flux:modal>

</div>
