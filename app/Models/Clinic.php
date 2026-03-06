<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $fillable = [
        'client_id',
        'clinic_name',
        'clinic_address',
        'city',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function licenseKeys()
    {
        return $this->hasMany(LicenseKey::class);
    }
}
