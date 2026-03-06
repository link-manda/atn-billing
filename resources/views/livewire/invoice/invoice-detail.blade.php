<div class="space-y-6">

    {{-- ── Top Bar ──────────────────────────────────────────────────────── --}}
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('invoices.index') }}"
                   class="inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Invoices
                </a>
                <span class="text-zinc-300 dark:text-zinc-600">/</span>
                <flux:badge variant="outline" size="sm">{{ $invoice->invoice_number }}</flux:badge>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Invoice Detail</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                Created {{ $invoice->created_at->format('d M Y') }}
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 flex-wrap">
            @if($invoice->status === 'draft')
                <flux:button wire:click="markSent" variant="ghost" icon="paper-airplane" size="sm">
                    Mark as Sent
                </flux:button>
            @endif

            @if(in_array($invoice->status, ['draft', 'sent', 'overdue']))
                <flux:button wire:click="markPaid"
                             wire:confirm="Mark this invoice as paid?"
                             variant="filled" icon="check-circle" size="sm"
                             class="bg-emerald-600 hover:bg-emerald-700 text-white">
                    Mark as Paid
                </flux:button>
            @endif

            <flux:button wire:click="downloadPdf" variant="primary" icon="arrow-down-tray" size="sm">
                Download PDF
            </flux:button>
        </div>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    {{-- ── Main Layout ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Invoice Preview Card (LEFT 2/3) ────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Invoice Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm">

                {{-- Card Header --}}
                <div class="bg-gradient-to-r from-blue-900 to-blue-700 px-8 py-7">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-blue-200 text-xs font-semibold tracking-wider uppercase mb-0.5">Andhira Teknologi Nusantara</p>
                            <p class="text-blue-100 text-xs">ATN Billing Platform</p>
                        </div>
                        <div class="text-right">
                            <p class="text-blue-100 text-xs tracking-widest font-bold uppercase">Invoice</p>
                            <p class="text-white text-xl font-bold mt-0.5">{{ $invoice->invoice_number }}</p>
                        </div>
                    </div>
                </div>

                {{-- Status strip --}}
                @php
                    $statusStrip = [
                        'draft'     => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400',
                        'sent'      => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                        'paid'      => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                        'overdue'   => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                        'cancelled' => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400',
                    ];
                    $stripClass = $statusStrip[$invoice->status] ?? 'bg-zinc-100 text-zinc-500';
                @endphp
                <div class="px-8 py-2 text-xs font-bold tracking-widest uppercase {{ $stripClass }}">
                    {{ strtoupper($invoice->status) }}
                </div>

                <div class="px-8 py-6 space-y-6">

                    {{-- Invoice Meta --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-1">Invoice Date</p>
                            <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $invoice->created_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-1">Due Date</p>
                            <p class="text-sm font-semibold {{ $invoice->status === 'overdue' ? 'text-red-600' : 'text-zinc-800 dark:text-zinc-200' }}">
                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-1">Billing Period</p>
                            <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                                {{ $invoice->created_at->format('M Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                    {{-- Bill To / Billed By --}}
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-blue-50 dark:bg-blue-950/40 rounded-xl p-4">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-2">Bill To</p>
                            <p class="font-bold text-zinc-900 dark:text-white text-sm">{{ $invoice->clinic?->client?->name ?? '—' }}</p>
                            <p class="text-zinc-600 dark:text-zinc-400 text-xs mt-1">{{ $invoice->clinic?->clinic_name ?? '—' }}</p>
                            @if($invoice->clinic?->clinic_address)
                                <p class="text-zinc-500 dark:text-zinc-500 text-xs">{{ $invoice->clinic->clinic_address }}</p>
                            @endif
                            @if($invoice->clinic?->city)
                                <p class="text-zinc-500 dark:text-zinc-500 text-xs">{{ $invoice->clinic->city }}</p>
                            @endif
                            @if($invoice->clinic?->client?->email)
                                <p class="text-blue-600 dark:text-blue-400 text-xs mt-1">{{ $invoice->clinic->client->email }}</p>
                            @endif
                        </div>

                        <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-xl p-4">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-2">Billed By</p>
                            <p class="font-bold text-zinc-900 dark:text-white text-sm">Andhira Teknologi Nusantara</p>
                            <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1">ATN Billing Platform</p>
                            <p class="text-zinc-500 dark:text-zinc-400 text-xs">support@atn.co.id</p>
                            <p class="text-zinc-500 dark:text-zinc-400 text-xs">www.atn.co.id</p>
                        </div>
                    </div>

                    {{-- Line Items Table --}}
                    <div class="rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700">
                        <table class="w-full text-sm">
                            <thead class="bg-blue-900 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Billing Period</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Unit Price</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                                <tr class="bg-white dark:bg-zinc-900">
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-zinc-900 dark:text-white">{{ $invoice->subscription?->product?->product_name ?? 'Subscription' }}</p>
                                        @if($invoice->subscription?->product?->description)
                                            <p class="text-xs text-zinc-400 mt-0.5">{{ $invoice->subscription->product->description }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-zinc-600 dark:text-zinc-400 text-xs">
                                        @php
                                            $ps = $invoice->created_at->copy()->startOfMonth();
                                            $pe = $ps->copy()->endOfMonth();
                                        @endphp
                                        {{ $ps->format('d M Y') }} –<br>{{ $pe->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-4 text-right text-zinc-800 dark:text-zinc-200">
                                        Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-right text-zinc-600 dark:text-zinc-400">1</td>
                                    <td class="px-4 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                        Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="flex justify-end">
                        <div class="w-64 space-y-2">
                            <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-400">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-400">
                                <span>Tax</span>
                                <span>Rp {{ number_format($invoice->tax ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2">
                                <div class="flex justify-between items-center bg-blue-900 text-white rounded-xl px-4 py-3">
                                    <span class="font-bold tracking-wide">Total</span>
                                    <span class="text-lg font-bold">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Info --}}
                    <div class="rounded-xl border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-950/40 p-5">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-blue-700 dark:text-blue-400 mb-3">Payment Instructions</p>
                        <div class="grid grid-cols-2 gap-y-2 text-sm">
                            <span class="text-zinc-500 dark:text-zinc-400">Method</span>
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">Bank Transfer</span>

                            @php
                                $bankAccounts = \App\Models\BankAccount::where('is_active', true)->where('is_default', true)->get();
                                if ($bankAccounts->isEmpty()) {
                                    $bankAccounts = \App\Models\BankAccount::where('is_active', true)->get();
                                }
                            @endphp

                            @forelse($bankAccounts as $bank)
                                <div class="col-span-2 my-1 border-t border-blue-200 dark:border-blue-800/50"></div>

                                <span class="text-zinc-500 dark:text-zinc-400">Bank</span>
                                <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $bank->bank_name }}{{ $bank->branch ? ' (' . $bank->branch . ')' : '' }}</span>

                                <span class="text-zinc-500 dark:text-zinc-400">Account Number</span>
                                <span class="font-bold text-zinc-900 dark:text-white tracking-widest">{{ $bank->account_number }}</span>

                                <span class="text-zinc-500 dark:text-zinc-400">Account Name</span>
                                <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $bank->account_name }}</span>

                                @if($bank->swift_code)
                                    <span class="text-zinc-500 dark:text-zinc-400">SWIFT Code</span>
                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $bank->swift_code }}</span>
                                @endif
                            @empty
                                <div class="col-span-2 my-1 border-t border-blue-200 dark:border-blue-800/50"></div>
                                <span class="text-zinc-500 dark:text-zinc-400">Bank</span>
                                <span class="font-medium text-zinc-800 dark:text-zinc-200">Bank Central Asia (BCA)</span>

                                <span class="text-zinc-500 dark:text-zinc-400">Account Number</span>
                                <span class="font-bold text-zinc-900 dark:text-white tracking-widest">1234567890</span>

                                <span class="text-zinc-500 dark:text-zinc-400">Account Name</span>
                                <span class="font-medium text-zinc-800 dark:text-zinc-200">PT Andhira Teknologi Nusantara</span>
                            @endforelse

                            <div class="col-span-2 my-1 border-t border-blue-200 dark:border-blue-800/50"></div>

                            <span class="text-zinc-500 dark:text-zinc-400">Reference</span>
                            <span class="font-bold text-blue-700 dark:text-blue-400">{{ $invoice->invoice_number }}</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- ── Sidebar (RIGHT 1/3) ──────────────────────────────────────── --}}
        <div class="space-y-4">

            {{-- Invoice Metadata --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Invoice Details</h3>
                </div>
                <div class="px-5 py-4 space-y-4">
                    {{-- Status --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Status</p>
                        @php
                            $statusColors = [
                                'draft'     => 'zinc',
                                'sent'      => 'blue',
                                'paid'      => 'green',
                                'overdue'   => 'red',
                                'cancelled' => 'stone',
                            ];
                        @endphp
                        <flux:badge color="{{ $statusColors[$invoice->status] ?? 'zinc' }}">
                            {{ ucfirst($invoice->status) }}
                        </flux:badge>
                        @if($invoice->paid_at)
                            <p class="text-xs text-zinc-400 mt-1">
                                Paid on {{ \Carbon\Carbon::parse($invoice->paid_at)->format('d M Y, H:i') }}
                            </p>
                        @endif
                    </div>

                    {{-- Subscription --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Subscription</p>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                            {{ $invoice->subscription?->product?->product_name ?? '—' }}
                        </p>
                        <p class="text-xs text-zinc-400">
                            Code: {{ $invoice->subscription?->product?->product_code ?? '—' }}
                        </p>
                    </div>

                    {{-- Clinic --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Clinic</p>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $invoice->clinic?->clinic_name ?? '—' }}</p>
                        <p class="text-xs text-zinc-400">{{ $invoice->clinic?->city ?? '' }}</p>
                    </div>

                    {{-- Billing Cycle --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Billing Cycle</p>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                            {{ ucfirst($invoice->subscription?->billing_cycle ?? 'Monthly') }}
                        </p>
                    </div>

                    {{-- PDF Status --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">PDF</p>
                        @if($invoice->pdf_path)
                            <flux:badge color="green" size="sm" icon="check-circle">Generated</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">Not Generated</flux:badge>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Payment History --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Payment History</h3>
                </div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($invoice->payments as $payment)
                        <div class="px-5 py-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-zinc-400 mt-0.5">
                                        {{ $payment->payment_method ?? 'Bank Transfer' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <flux:badge color="green" size="sm">Paid</flux:badge>
                                    <p class="text-xs text-zinc-400 mt-1">
                                        {{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center">
                            <flux:icon.banknotes class="w-8 h-8 mx-auto mb-2 text-zinc-300 dark:text-zinc-600" />
                            <p class="text-sm text-zinc-400">No payments recorded</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</div>
