<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestTabSwitch extends Model
{
    protected $fillable = [
        'supervisor_test_attempt_id',
        'switched_at',
    ];

    protected $casts = [
        'switched_at' => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(SupervisorTestAttempt::class, 'supervisor_test_attempt_id');
    }
}
