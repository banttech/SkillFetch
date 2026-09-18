<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'fees',
        'timing',
        'text_marks',
        'text_q_per_dept',
        'single_select_marks',
        'single_select_q_per_dept',
        'multi_select_marks',
        'multi_select_q_per_dept',
        'image_marks',
        'image_q_per_dept',
        'video_marks',
        'video_q_per_dept',
        'no_of_departments',
        'question_per_department',
        'total_question',
        'total_marks',
        'passing_marks',
        'status',
    ];

    protected $casts = [
        'fees'                      => 'integer',
        'timing'                    => 'integer',
        'text_marks'                => 'integer',
        'text_q_per_dept'           => 'integer',
        'single_select_marks'       => 'integer',
        'single_select_q_per_dept'  => 'integer',
        'multi_select_marks'        => 'integer',
        'multi_select_q_per_dept'   => 'integer',
        'image_marks'               => 'integer',
        'image_q_per_dept'          => 'integer',
        'video_marks'               => 'integer',
        'video_q_per_dept'          => 'integer',
        'no_of_departments'         => 'integer',
        'question_per_department'   => 'integer',
        'total_question'            => 'integer',
        'total_marks'               => 'integer',
        'passing_marks'             => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function attempts(): HasMany
    {
        return $this->hasMany(SupervisorTestAttempt::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(SupervisorTestFees::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Returns a map of answer_type => [marks_per_q, q_per_dept]
     * Used by TestService to build randomized question sets.
     */
    public function getQuestionTypeConfig(): array
    {
        return [
            'text'   => ['marks' => $this->text_marks,          'count' => $this->text_q_per_dept],
            'single' => ['marks' => $this->single_select_marks,  'count' => $this->single_select_q_per_dept],
            'multi'  => ['marks' => $this->multi_select_marks,   'count' => $this->multi_select_q_per_dept],
            'image'  => ['marks' => $this->image_marks,          'count' => $this->image_q_per_dept],
            'video'  => ['marks' => $this->video_marks,          'count' => $this->video_q_per_dept],
        ];
    }
}