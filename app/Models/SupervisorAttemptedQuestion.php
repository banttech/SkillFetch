<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupervisorAttemptedQuestion extends Model
{
    protected $fillable = [
        'supervisor_test_attempt_id',
        'question_id',
        'question',
        'department',
        'answer_type',
        'is_attempted',
        'is_corrected_by_user',
        'marks_per_question',   // NEW — stores per-question marks from test config
    ];

    protected $casts = [
        'is_attempted'        => 'boolean',
        'is_corrected_by_user'=> 'boolean',
        'marks_per_question'  => 'integer',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(SupervisorTestAttempt::class, 'supervisor_test_attempt_id');
    }

    /**
     * The original Question record (for fetching media_path on image/video questions).
     */
    public function questionModel(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function attemptedAnswers(): HasMany
    {
        return $this->hasMany(SupervisorAttemptedAnswer::class);
    }
}