<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerJobPayment extends Model
{
    protected $fillable = [
        'job_id',
        'transaction_id',
        'payment_status',
        'payment_order_id'
    ];
}
