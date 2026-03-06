<?php

namespace App\Services;

use App\Repositories\InvoiceRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    public function __construct(
        private InvoiceRepository $invoiceRepository,
    ) {}

    /**
     * Generate a PDF for the given invoice, store it in storage/app/invoices/,
     * save the path on the invoice record, and return the storage path.
     */
    public function generateInvoicePdf(int $invoice_id): string
    {
        $invoice = $this->invoiceRepository->find($invoice_id);

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))
            ->setPaper('A4', 'portrait');

        $filename  = $invoice->invoice_number . '.pdf';
        $directory = 'invoices';
        $storagePath = $directory . '/' . $filename;

        Storage::disk('local')->put($storagePath, $pdf->output());

        $this->invoiceRepository->updatePdfPath($invoice_id, $storagePath);

        return $storagePath;
    }

    /**
     * Return a DomPDF instance for streaming or downloading directly (without storing).
     */
    public function streamInvoicePdf(int $invoice_id): \Barryvdh\DomPDF\PDF
    {
        $invoice = $this->invoiceRepository->find($invoice_id);

        return Pdf::loadView('pdf.invoice', compact('invoice'))
            ->setPaper('A4', 'portrait');
    }
}
