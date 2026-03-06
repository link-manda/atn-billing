<div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden flex flex-col h-full">
    <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
        <div>
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Recent Invoices</h3>
            <p class="text-xs text-zinc-500 mt-0.5">Latest generated billing</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 transition-colors">View All &rarr;</a>
    </div>

    <div class="flex-1 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-700">
                <tr>
                    <th class="px-5 py-3 font-medium">Invoice</th>
                    <th class="px-5 py-3 font-medium">Billed To</th>
                    <th class="px-5 py-3 font-medium text-right">Amount</th>
                    <th class="px-5 py-3 font-medium text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($recentInvoices as $invoice)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors">
                        <td class="px-5 py-3">
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="font-medium text-zinc-900 dark:text-white hover:text-blue-600 transition-colors">
                                {{ $invoice->invoice_number }}
                            </a>
                            <div class="text-[11px] text-zinc-400 mt-0.5">{{ $invoice->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-zinc-800 dark:text-zinc-200">{{ $invoice->clinic?->client?->name ?? 'Unknown Client' }}</div>
                            <div class="text-[11px] text-zinc-500 mt-0.5">{{ $invoice->clinic?->clinic_name ?? '—' }}</div>
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-zinc-900 dark:text-white">
                            Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $statusColors = [
                                    'draft'     => 'zinc',
                                    'sent'      => 'blue',
                                    'paid'      => 'green',
                                    'overdue'   => 'red',
                                    'cancelled' => 'stone',
                                ];
                            @endphp
                            <flux:badge color="{{ $statusColors[$invoice->status] ?? 'zinc' }}" size="sm">
                                {{ ucfirst($invoice->status) }}
                            </flux:badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center">
                            <flux:icon.document-text class="w-8 h-8 mx-auto mb-2 text-zinc-300 dark:text-zinc-600" />
                            <p class="text-sm text-zinc-500">No invoices found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
