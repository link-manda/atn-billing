<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'subscription_id',
        'clinic_id',
        'amount',
        'tax',
        'total',
        'status',
        'due_date',
        'paid_at',
        'pdf_path',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
