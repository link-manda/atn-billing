<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientDashboardController
{

    public function __invoke()
    {

        $clinic = auth()->user()->clinic;

        $subscription = $clinic->subscriptions()->first();

        $latest_invoice = $subscription->invoices()
            ->latest()
            ->first();

        return view('portal.dashboard', compact(
            'clinic',
            'subscription',
            'latest_invoice'
        ));
    }

}