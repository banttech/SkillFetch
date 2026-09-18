<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisorTestFees extends Model
{
    protected $fillable = [
        'supervisor_id',
        'test_id',
        'razorpay_order_id',
        'transaction_id',
        'payment_status',
        'name',
        'fees',
        'timing',
        'marks',
        'question_per_department',
        'no_of_departments',
        'total_question',
        'total_marks',
        'passing_marks',
    ];

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeForTest($query, int $testId)
    {
        return $query->where('test_id', $testId);
    }
}