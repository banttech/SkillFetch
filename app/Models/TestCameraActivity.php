<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestCameraActivity extends Model
{
    protected $fillable = [
        'supervisor_test_attempt_id',
        'activity_type',
        'details',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(SupervisorTestAttempt::class, 'supervisor_test_attempt_id');
    }
}
