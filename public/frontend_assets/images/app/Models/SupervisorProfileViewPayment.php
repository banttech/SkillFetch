<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorProfileViewPayment extends Model
{
    protected $fillable = [
        'employer_id',
        'supervisor_id',
        'razorpay_order_id',
        'transaction_id',
        'payment_status',
        'amount',
        'paid_at',
        'failed_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function employer()
    {
        return $this->belongsTo(EmployerDetail::class, 'employer_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }
}