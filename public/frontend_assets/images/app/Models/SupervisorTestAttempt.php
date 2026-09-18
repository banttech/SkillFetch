<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class SupervisorTestAttempt extends Model
{
    const MAX_TAB_SWITCHES = 2; // 3rd switch triggers auto-submit

    protected $fillable = [
        'supervisor_id',
        'test_id',
        'test_name',
        'fees',
        'question_per_department',
        'timing',
        'no_of_departments',
        'total_question',
        'per_question_marks',
        'total_marks',
        'passing_marks',
        'start_time',
        'end_time',
        'user_taken_time',
        'test_submission_status',
        'status',
        'reject_reason',
        'score_obtained',
        'percentage',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'can_reattempt_after',
    ];

    protected $casts = [
        'start_time'          => 'datetime',
        'end_time'            => 'datetime',
        'submitted_at'        => 'datetime',
        'reviewed_at'         => 'datetime',
        'can_reattempt_after' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function attemptedQuestions(): HasMany
    {
        return $this->hasMany(SupervisorAttemptedQuestion::class);
    }

    public function testSkills(): HasMany
    {
        return $this->hasMany(TestSkill::class, 'attempt_id');
    }

    public function tabSwitches(): HasMany
    {
        return $this->hasMany(TestTabSwitch::class, 'supervisor_test_attempt_id');
    }

    public function cameraActivities(): HasMany
    {
        return $this->hasMany(TestCameraActivity::class, 'supervisor_test_attempt_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /** Only submitted attempts (not still-pending). */
    public function scopeSubmitted($query)
    {
        return $query->whereIn('test_submission_status', ['supervisor', 'automatic']);
    }

    public function scopeForTest($query, int $testId)
    {
        return $query->where('test_id', $testId);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /**
     * Human-readable duration: hours:minutes:seconds (e.g. 0:45:32).
     * Uses start_time → end_time; falls back to submitted_at if end_time is null.
     */
    public function getFormattedDurationAttribute(): string
    {
        $start = $this->start_time;
        $end   = $this->submitted_at ?? $this->end_time;

        if (!$start || !$end) {
            return '—';
        }

        $seconds = abs($end->timestamp - $start->timestamp);
        $h = (int) floor($seconds / 3600);
        $m = (int) floor(($seconds % 3600) / 60);
        $s = $seconds % 60;

        return sprintf('%d:%02d:%02d', $h, $m, $s);
    }

    /**
     * Score is only visible after admin has reviewed (pass or fail).
     * While underReview the score may still change, so we hide it.
     */
    public function getScoreVisibleAttribute(): bool
    {
        return in_array($this->status, ['pass', 'fail']);
    }

    /** Human-readable status label. */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pass'        => 'Passed',
            'fail'        => 'Failed',
            'underReview' => 'Under Review',
            'pending'     => 'In Progress',
            default       => ucfirst($this->status),
        };
    }

    /** True when admin has not yet reviewed this attempt. */
    public function getIsUnderReviewAttribute(): bool
    {
        return $this->status === 'underReview';
    }

    /**
     * True when this attempt is within the configured cooldown window.
     * Uses the can_reattempt_after timestamp set by admin on pass/fail.
     */
    public function getIsInCooldownAttribute(): bool
    {
        if (!in_array($this->status, ['pass', 'fail'])) {
            return false;
        }
        if (!$this->can_reattempt_after) {
            return false;
        }
        return now()->lessThan($this->can_reattempt_after);
    }

    /**
     * Master block-state accessor.
     * Returns: 'under_review' | 'cooldown' | 'free'
     *
     * This is used by AvailableTestsController to determine GLOBALLY
     * whether ANY new attempt is allowed for this supervisor.
     */
    public function getBlockStateAttribute(): string
    {
        if ($this->is_under_review) {
            return 'under_review';
        }
        if ($this->is_in_cooldown) {
            return 'cooldown';
        }
        return 'free';
    }

    // ── Static: global cooldown check ────────────────────────────────────────

    /**
     * Check if a supervisor is currently in a global cooldown period
     * across ALL tests. Returns the blocking attempt or null if free.
     *
     * Logic:
     *   1. Find the most recent submitted attempt across ALL tests for this supervisor.
     *   2. If it is underReview  → blocked (we don't know result yet).
     *   3. If it was reviewed (pass/fail) and can_reattempt_after is in the future → blocked.
     *   4. Otherwise → free to attempt any test.
     */
    public static function getGlobalBlockForSupervisor(int $supervisorId): ?self
    {
        $latest = static::where('supervisor_id', $supervisorId)
            ->submitted()
            ->latest('submitted_at')
            ->first();

        if (!$latest) {
            return null; // Never attempted anything
        }

        if ($latest->block_state !== 'free') {
            return $latest; // Still blocked
        }

        return null; // Free to go
    }
}