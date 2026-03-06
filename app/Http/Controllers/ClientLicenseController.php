<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientLicenseController
{

    public function __invoke()
    {

        $clinic = auth()->user()->clinic;

        $licenses = LicenseKey::where(
            'clinic_id',
            $clinic->id
        )->get();

        return view(
            'portal.licenses.index',
            compact('licenses')
        );

    }

}