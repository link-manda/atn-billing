<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseKey extends Model
{
    protected $fillable = [
        'subscription_id',
        'clinic_id',
        'product_id',
        'license_key',
        'status',
        'expired_at',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
