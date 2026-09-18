<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\SupervisorTestAttempt;
use App\Models\SupervisorAttemptedQuestion;
use App\Models\Supervisor;
use App\Models\Test;
use App\Models\Skill;
use App\Models\TestSkill;
use App\Models\TestTabSwitch;
use App\Models\TestCameraActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SupervisorTestController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    //  Supervisors list (unchanged logic, kept for completeness)
    // ─────────────────────────────────────────────────────────────────────────

    public function supervisorsList(Request $request)
    {
        try {
            $query = Supervisor::query()
                ->with(['user', 'latestTestAttempt'])
                ->select('supervisors.*');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('verification_status')) {
                $status = $request->verification_status;
                if ($status === 'not_attempted') {
                    $query->doesntHave('latestTestAttempt');
                } else {
                    $query->whereRaw('supervisors.id IN (
                        SELECT sta.supervisor_id
                        FROM supervisor_test_attempts sta
                        INNER JOIN (
                            SELECT supervisor_id, MAX(id) as max_id
                            FROM supervisor_test_attempts
                            GROUP BY supervisor_id
                        ) latest ON sta.supervisor_id = latest.supervisor_id AND sta.id = latest.max_id
                        WHERE sta.status = ?
                    )', [$status]);
                }
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $sortBy    = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'DESC');

           

            if (in_array($sortBy, ['name', 'email'])) {
                $query->join('users', 'supervisors.user_id', '=', 'users.id')
                      ->orderBy('users.' . $sortBy, $sortOrder)
                      ->select('supervisors.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            $supervisors = $query->paginate($request->get('per_page', 10))->withQueryString();
            $pageTitle   = 'Supervisors List';

            return view('admin.supervisor.supervisors-list', compact('supervisors', 'pageTitle'));

        } catch (Exception $e) {
            Log::error('Supervisors list error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load supervisor list.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Test submissions list for a supervisor
    // ─────────────────────────────────────────────────────────────────────────

    public function index(int $supervisorId)
    {
        $pageTitle = 'Test Submissions';

        $sup = Supervisor::where('id', $supervisorId)->with('user')->first();

        if (!$sup) {
            return redirect()->back()->with('error', 'Supervisor not found.');
        }

        $testAttempts = SupervisorTestAttempt::where('supervisor_id', $supervisorId)
            ->whereIn('test_submission_status', ['supervisor', 'automatic'])
            ->with(['test', 'testSkills.skill'])
            ->latest('submitted_at')
            ->paginate(20);

        return view('admin.supervisor.test-submissions', compact('testAttempts', 'pageTitle', 'sup'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Review a single attempt
    // ─────────────────────────────────────────────────────────────────────────

    public function review(int $attemptId)
    {
        $pageTitle = 'Review Test';

        $attempt = SupervisorTestAttempt::with([
            'supervisor.user',
            'supervisor.skills',
            'attemptedQuestions.attemptedAnswers',
            'attemptedQuestions.questionModel',
            'test',
            'testSkills.skill',
            'tabSwitches',
            'cameraActivities',
        ])->findOrFail($attemptId);

        $id        = $attempt->supervisor_id;
        $name      = $attempt->supervisor->user->name    ?? 'N/A';
        $email     = $attempt->supervisor->user->email   ?? 'N/A';
        $mobile    = $attempt->supervisor->user->phone  ?? 'N/A';
        $test_date = $attempt->submitted_at?->format('d M Y, h:i A') ?? 'N/A';
        $skills    = $attempt->supervisor->skills->pluck('name')->toArray();

        $groupedQuestions = $attempt->attemptedQuestions->groupBy('department');
        $allSkills = Skill::orderby('name', 'asc')->get();
        $tabSwitches = $attempt->tabSwitches;
        $cameraActivities = $attempt->cameraActivities;

        // Current score — type-aware
        $currentScore = $this->calculateScore($attempt);

        return view('admin.supervisor.supervisors-qualification-test', compact(
            'attempt',
            'id',
            'name',
            'email',
            'mobile',
            'test_date',
            'groupedQuestions',
            'currentScore',
            'pageTitle',
            'skills',
            'allSkills',
            'tabSwitches',
            'cameraActivities'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Toggle correction (AJAX)
    // ─────────────────────────────────────────────────────────────────────────

    public function toggleCorrection(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:supervisor_attempted_questions,id',
            'is_correct'  => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            $attemptedQuestion = SupervisorAttemptedQuestion::with('attempt')
                ->findOrFail($request->question_id);

            $attemptedQuestion->update(['is_corrected_by_user' => $request->is_correct]);

            $attempt   = $attemptedQuestion->attempt;
            $newScore  = $this->calculateScore($attempt);
            $percentage = $attempt->total_marks > 0
                ? ($newScore / $attempt->total_marks) * 100
                : 0;

            $totalCorrect = $attempt->attemptedQuestions()->where('is_corrected_by_user', 1)->count();

            $attempt->update([
                'score_obtained' => $newScore,
                'percentage'     => round($percentage, 2),
            ]);

            DB::commit();

            return response()->json([
                'success'       => true,
                'new_score'     => $newScore,
                'total_marks'   => $attempt->total_marks,
                'total_correct' => $totalCorrect,
                'can_pass'      => $newScore >= $attempt->passing_marks,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Verify (pass)
    // ─────────────────────────────────────────────────────────────────────────

    public function verify(Request $request, int $attemptId)
    {
        $request->validate([
            'suggested_skills'   => 'nullable|array',
            'suggested_skills.*' => 'exists:skills,id'
        ]);

        try {
            $attempt = SupervisorTestAttempt::findOrFail($attemptId);

            if ($attempt->score_obtained < $attempt->passing_marks) {
                return back()->with('error', 'Score is below passing marks. Cannot verify.');
            }

            $attempt->update([
                'status'      => 'pass',
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'can_reattempt_after' =>now()->addHours(AppSetting::testAttemptWindowHours()),
            ]);

            if ($request->filled('suggested_skills')) {
                foreach ($request->suggested_skills as $skillId) {
                    TestSkill::firstOrCreate([
                        'attempt_id'    => $attempt->id,
                        'supervisor_id' => $attempt->supervisor_id,
                        'skill_id'      => $skillId
                    ]);
                }
            }

           

            return redirect()->back()->with('success', 'The test has been approved.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to verify test: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Reject (fail)
    // ─────────────────────────────────────────────────────────────────────────

    public function reject(Request $request, int $attemptId)
    {
        $request->validate([
            'reject_reason'      => 'required|string|max:1000',
            'suggested_skills'   => 'nullable|array',
            'suggested_skills.*' => 'exists:skills,id'
        ]);

        try {
            $attempt = SupervisorTestAttempt::findOrFail($attemptId);

            $attempt->update([
                'status'              => 'fail',
                'reject_reason'       => $request->reject_reason,
                'reviewed_at'         => now(),
                'reviewed_by'         => Auth::id(),
               'can_reattempt_after' =>now()->addHours(AppSetting::testAttemptWindowHours()),
            ]);

            if ($request->filled('suggested_skills')) {
                foreach ($request->suggested_skills as $skillId) {
                    TestSkill::firstOrCreate([
                        'attempt_id'    => $attempt->id,
                        'supervisor_id' => $attempt->supervisor_id,
                        'skill_id'      => $skillId
                    ]);
                }
            }

            $waitHours = AppSetting::testAttemptWindowHours();
            return redirect()->back()->with('success', "This test has been successfully rejected by Admin. Supervisor may now reattempt this test or any other test after {$waitHours} hours.");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject test: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Department-wise score breakdown (AJAX)
    // ─────────────────────────────────────────────────────────────────────────

    public function departmentScore(int $attemptId)
    {
        try {
            $attempt = SupervisorTestAttempt::with('attemptedQuestions')
                ->findOrFail($attemptId);

            // Group all logged questions by department name
            $grouped = $attempt->attemptedQuestions->groupBy('department');

            $departments = $grouped->map(function ($questions, $deptName) use ($attempt) {
                $totalQ      = $questions->count();
                $correctQ    = $questions->where('is_corrected_by_user', true)->count();
                $marksEarned = $questions
                    ->where('is_corrected_by_user', true)
                    ->sum(fn ($q) => $q->marks_per_question ?? $attempt->per_question_marks);
                $totalMarks  = $questions
                    ->sum(fn ($q) => $q->marks_per_question ?? $attempt->per_question_marks);
                $percentage  = $totalMarks > 0
                    ? round(($marksEarned / $totalMarks) * 100, 1)
                    : 0;

                return [
                    'department'   => $deptName ?: 'General',
                    'total_q'      => $totalQ,
                    'correct_q'    => $correctQ,
                    'marks_earned' => $marksEarned,
                    'total_marks'  => $totalMarks,
                    'percentage'   => $percentage,
                ];
            })->values();

            return response()->json([
                'success'     => true,
                'test_name'   => $attempt->test_name,
                'score'       => $attempt->score_obtained,
                'total'       => $attempt->total_marks,
                'percentage'  => $attempt->percentage,
                'status'      => $attempt->status,
                'departments' => $departments,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load department scores.',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Private: type-aware score calculation
    // ─────────────────────────────────────────────────────────────────────────

    private function calculateScore(SupervisorTestAttempt $attempt): int|float
    {
        // Reload questions fresh to avoid stale cache
        $questions = $attempt->attemptedQuestions()->where('is_corrected_by_user', 1)->get();

        $score = 0;
        foreach ($questions as $q) {
            // Use per-question marks if stored, fall back to attempt-level marks
            $score += $q->marks_per_question ?? $attempt->per_question_marks;
        }

        return $score;
    }
}