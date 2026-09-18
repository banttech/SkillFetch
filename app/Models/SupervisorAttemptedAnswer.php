<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisorAttemptedAnswer extends Model
{
    protected $fillable = [
        'supervisor_attempted_question_id',
        'answer_id',
        'answer',
        'is_correct',
        'user_answer',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function attemptedQuestion(): BelongsTo
    {
        return $this->belongsTo(SupervisorAttemptedQuestion::class, 'supervisor_attempted_question_id');
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}