<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientSubscriptionController
{

    public function __invoke()
    {

        $clinic = auth()->user()->clinic;

        $subscriptions = $clinic
            ->subscriptions()
            ->get();

        return view(
            'portal.subscriptions.index',
            compact('subscriptions')
        );

    }

}