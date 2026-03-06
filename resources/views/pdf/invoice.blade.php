<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        /* Base dompdf styles */
        @page { margin: 40px 50px; }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #374151;
            line-height: 1.5;
            background: #ffffff;
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-gray { color: #6b7280; }
        .text-dark { color: #111827; }
        .text-primary { color: #1e3a8a; }
        .text-red { color: #dc2626; }
        .text-sm { font-size: 10px; }
        .text-lg { font-size: 14px; }

        /* Header */
        .header { margin-bottom: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; }
        .company-name { font-size: 22px; font-weight: bold; color: #1e3a8a; letter-spacing: -0.5px; margin-bottom: 2px; }
        .company-tag { font-size: 11px; color: #6b7280; }

        .invoice-title { font-size: 32px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .invoice-number-label { font-size: 12px; color: #6b7280; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .invoice-number { font-size: 14px; font-weight: bold; color: #111827; }

        .status-badge { display: inline-block; padding: 4px 8px; font-size: 10px; font-weight: bold; text-transform: uppercase; border-radius: 4px; margin-top: 8px; }
        .status-draft    { background: #f3f4f6; color: #6b7280; }
        .status-sent     { background: #dbeafe; color: #1d4ed8; }
        .status-paid     { background: #dcfce7; color: #16a34a; }
        .status-overdue  { background: #fee2e2; color: #dc2626; }
        .status-cancelled{ background: #f3f4f6; color: #9ca3af; }

        /* Information Section */
        .info-section { margin-bottom: 30px; }
        .section-title { font-size: 10px; font-weight: bold; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; border-bottom: 1px solid #f3f4f6; padding-bottom: 4px; }

        .meta-table td { padding: 4px 0; border-bottom: 1px solid #f9fafb; }
        .meta-table tr:last-child td { border-bottom: none; }

        /* Items Table */
        .table-items { margin-bottom: 25px; }
        .table-items th { background: #f8fafc; color: #475569; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0; }
        .table-items td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; }
        .item-name { font-size: 12px; font-weight: bold; color: #111827; margin-bottom: 3px; }
        .item-desc { font-size: 10px; color: #6b7280; line-height: 1.4; }

        /* Totals */
        .totals-table { width: 100%; }
        .totals-table td { padding: 8px 10px; border-bottom: 1px solid #f8fafc; }
        .totals-table .total-label { color: #6b7280; font-size: 11px; }
        .totals-table .total-value { color: #111827; font-size: 12px; font-weight: bold; }

        .grand-total { background: #1e3a8a; }
        .grand-total td { padding: 14px 10px; border-bottom: none; }
        .grand-total .total-label { color: #ffffff; font-weight: bold; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .grand-total .total-value { color: #ffffff; font-weight: bold; font-size: 16px; }

        /* Payment Info */
        .payment-box { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #1e3a8a; padding: 15px; border-radius: 4px; }
        .payment-table td { padding: 4px 0; font-size: 11px; }

        /* Footer */
        .notes { font-size: 10px; color: #6b7280; text-align: center; margin-top: 30px; margin-bottom: 15px; }
        .footer { border-top: 1px solid #e2e8f0; padding-top: 15px; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header">
        <tr>
            <td width="50%">
                <div class="company-name">Andhira Teknologi Nusantara</div>
                <div class="company-tag">ATN Billing Platform</div>
            </td>
            <td width="50%" class="text-right">
                <div class="invoice-title">INVOICE</div>
                <div>
                    <span class="invoice-number-label">NO:</span>
                    <span class="invoice-number">#{{ $invoice->invoice_number }}</span>
                </div>
                <div class="status-badge status-{{ strtolower($invoice->status) }}">
                    {{ strtoupper($invoice->status) }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Info Section (Bill To, Billed By, Meta) -->
    <table class="info-section">
        <tr>
            <!-- Bill To -->
            <td width="35%" style="padding-right: 20px;">
                <div class="section-title">Bill To</div>
                <div class="font-bold text-dark text-lg" style="margin-bottom: 4px;">{{ $invoice->clinic?->client?->name ?? '—' }}</div>
                <div class="text-gray" style="line-height: 1.6;">
                    <span class="font-bold">{{ $invoice->clinic?->clinic_name ?? '—' }}</span><br>
                    {{ $invoice->clinic?->clinic_address ?? '' }}
                    @if($invoice->clinic?->city)
                        <br>{{ $invoice->clinic->city }}
                    @endif
                    @if($invoice->clinic?->client?->email)
                        <br>{{ $invoice->clinic->client->email }}
                    @endif
                    @if($invoice->clinic?->client?->phone)
                        <br>{{ $invoice->clinic->client->phone }}
                    @endif
                </div>
            </td>

            <!-- Billed By -->
            <td width="30%" style="padding-right: 20px;">
                <div class="section-title">Billed By</div>
                <div class="font-bold text-dark text-lg" style="margin-bottom: 4px;">ATN Billing Platform</div>
                <div class="text-gray" style="line-height: 1.6;">
                    Andhira Teknologi Nusantara<br>
                    support@atn.co.id<br>
                    www.atn.co.id
                </div>
            </td>

            <!-- Meta Details -->
            <td width="35%">
                <div class="section-title">Invoice Details</div>
                <table class="meta-table">
                    <tr>
                        <td class="text-gray">Invoice Date</td>
                        <td class="text-right font-bold text-dark">{{ $invoice->created_at->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-gray">Due Date</td>
                        <td class="text-right font-bold {{ $invoice->status === 'overdue' ? 'text-red' : 'text-dark' }}">
                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-gray">Billing Cycle</td>
                        <td class="text-right font-bold text-dark">{{ ucfirst($invoice->subscription?->billing_cycle ?? 'Monthly') }}</td>
                    </tr>
                    <tr>
                        <td class="text-gray">Billing Period</td>
                        @php
                            $start = \Carbon\Carbon::parse($invoice->created_at)->startOfMonth();
                        @endphp
                        <td class="text-right font-bold text-dark">{{ $start->format('M Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="table-items">
        <thead>
            <tr>
                <th width="45%">Description</th>
                <th width="20%">Period</th>
                <th width="15%" class="text-right">Unit Price</th>
                <th width="5%" class="text-center">Qty</th>
                <th width="15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="item-name">{{ $invoice->subscription?->product?->product_name ?? 'Subscription' }}</div>
                    <div class="item-desc">{{ $invoice->subscription?->product?->description ?? '' }}</div>
                </td>
                <td>
                    @php
                        $pStart = \Carbon\Carbon::parse($invoice->created_at)->startOfMonth();
                        $pEnd   = $pStart->copy()->endOfMonth();
                    @endphp
                    <span class="text-gray">{{ $pStart->format('d M') }} – {{ $pEnd->format('d M Y') }}</span>
                </td>
                <td class="text-right text-dark">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                <td class="text-center text-dark">1</td>
                <td class="text-right font-bold text-dark">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Totals & Payment Details Layout -->
    <table>
        <tr>
            <!-- Payment Instructions -->
            <td width="55%" style="padding-right: 40px;">
                <div class="payment-box">
                    <div class="section-title" style="border: none; margin-bottom: 12px; color: #1e3a8a;">Payment Instructions</div>
                    <table class="payment-table">
                        <tr>
                            <td width="35%" class="text-gray">Method</td>
                            <td class="font-bold text-dark">Bank Transfer</td>
                        </tr>
                        @php
                            $bankAccounts = \App\Models\BankAccount::where('is_active', true)->where('is_default', true)->get();
                            if ($bankAccounts->isEmpty()) {
                                $bankAccounts = \App\Models\BankAccount::where('is_active', true)->get();
                            }
                        @endphp

                        @forelse($bankAccounts as $bank)
                        <tr>
                            <td class="text-gray">Bank Name</td>
                            <td class="font-bold text-dark">{{ $bank->bank_name }}{{ $bank->branch ? ' (' . $bank->branch . ')' : '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray">Account No.</td>
                            <td class="font-bold text-dark text-lg" style="letter-spacing: 1px;">{{ $bank->account_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray">Account Name</td>
                            <td class="font-bold text-dark">{{ $bank->account_name }}</td>
                        </tr>
                        @if($bank->swift_code)
                        <tr>
                            <td class="text-gray">SWIFT Code</td>
                            <td class="font-bold text-dark">{{ $bank->swift_code }}</td>
                        </tr>
                        @endif
                        @if(!$loop->last)
                        <tr><td colspan="2" style="padding:4px 0;"><div style="border-bottom: 1px dashed #cbd5e1;"></div></td></tr>
                        @endif
                        @empty
                        <tr>
                            <td class="text-gray">Bank Name</td>
                            <td class="font-bold text-dark">Bank Central Asia (BCA)</td>
                        </tr>
                        <tr>
                            <td class="text-gray">Account No.</td>
                            <td class="font-bold text-dark text-lg" style="letter-spacing: 1px;">1234567890</td>
                        </tr>
                        <tr>
                            <td class="text-gray">Account Name</td>
                            <td class="font-bold text-dark">PT Andhira Teknologi Nusantara</td>
                        </tr>
                        @endforelse
                        <tr>
                            <td class="text-gray" style="padding-top: 8px;">Reference</td>
                            <td class="font-bold" style="padding-top: 8px; color: #1e3a8a;">{{ $invoice->invoice_number }}</td>
                        </tr>
                    </table>
                </div>
            </td>

            <!-- Totals -->
            <td width="45%">
                <table class="totals-table">
                    <tr>
                        <td class="total-label">Subtotal</td>
                        <td class="text-right total-value">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="total-label">Tax (11%)</td>
                        <td class="text-right total-value">Rp {{ number_format($invoice->tax ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td class="total-label">Total Due</td>
                        <td class="text-right total-value">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Notes & Footer -->
    <div class="notes">
        <p>Please include the invoice reference number (<strong>{{ $invoice->invoice_number }}</strong>) with your transfer.</p>
        <p>For any questions regarding this invoice, please contact support@atn.co.id.</p>
        <p class="font-bold text-dark" style="margin-top: 8px;">Thank you for your business!</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Andhira Teknologi Nusantara &bull; ATN Billing Platform &bull; Generated on {{ now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
    </div>

</body>
</html>
