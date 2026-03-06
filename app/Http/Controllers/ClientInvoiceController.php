<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientInvoiceController
{

    public function __invoke()
    {

        $clinic = auth()->user()->clinic;

        $invoices = Invoice::where(
            'clinic_id',
            $clinic->id
        )->latest()->get();

        return view(
            'portal.invoices.index',
            compact('invoices')
        );

    }

}
