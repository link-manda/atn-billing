<?php

namespace App\Livewire\Invoice;

use App\Services\InvoiceService;
use App\Services\InvoicePdfService;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceDetail extends Component
{
    public int $invoiceId;
    public bool $confirmPaid = false;

    public function mount(int $id): void
    {
        $this->invoiceId = $id;
    }

    public function markPaid(InvoiceService $service): void
    {
        $service->markPaid($this->invoiceId);
        $this->confirmPaid = false;
        session()->flash('success', 'Invoice marked as paid.');
    }

    public function markSent(InvoiceService $service): void
    {
        $service->markSent($this->invoiceId);
        session()->flash('success', 'Invoice status updated to sent.');
    }

    public function downloadPdf(InvoicePdfService $pdfService): StreamedResponse
    {
        $storagePath = $pdfService->generateInvoicePdf($this->invoiceId);

        $invoice = app(InvoiceService::class)->find($this->invoiceId);

        return response()->streamDownload(function () use ($storagePath) {
            echo \Illuminate\Support\Facades\Storage::disk('local')->get($storagePath);
        }, $invoice->invoice_number . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function render(InvoiceService $service)
    {
        $invoice = $service->find($this->invoiceId);

        return view('livewire.invoice.invoice-detail', compact('invoice'));
    }
}
