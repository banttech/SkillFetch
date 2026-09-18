<?php

namespace App\Services;

use App\Models\Test;
use App\Models\TestSetting;
use App\Models\Department;
use App\Models\Supervisor;
use App\Models\SupervisorTestAttempt;
use App\Models\SupervisorAttemptedQuestion;
use App\Models\SupervisorAttemptedAnswer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestService
{
    // =========================================================================
    //  NEW: Test-model based methods (multi-test system)
    // =========================================================================

    /**
     * Get departments with questions randomised per type for a given Test.
     *
     * For each department we fetch:
     *   - N text questions
     *   - N single-select questions
     *   - N multi-select questions
     *   - N image questions  (answer_type = 'image')
     *   - N video questions  (answer_type = 'video')
     * where each N comes from the test's per-type config.
     *
     * Questions with answer_type image/video use sub_answer_type for their
     * answer format, but are fetched by their primary answer_type.
     */
    public function getTestWithQuestionsForNewTest(Test $test): \Illuminate\Support\Collection
    {
        $departments = Department::latest()
            ->limit($test->no_of_departments)
            ->get();

        $typeConfig = $test->getQuestionTypeConfig();

        $departments->each(function ($department) use ($typeConfig) {
            $allQuestions = collect();

            foreach ($typeConfig as $type => $config) {
                if ($config['count'] <= 0) {
                    continue;
                }

                // For image/video the effective_answer_type stored on question IS 'image'/'video'
                $questions = $department->questions()
                    ->where('status', 1)
                    ->where('answer_type', $type)
                    ->with('answers')
                    ->inRandomOrder()
                    ->limit($config['count'])
                    ->get();

                // Tag each question with how many marks it carries
                $questions->each(fn ($q) => $q->marks_for_this_type = $config['marks']);

                $allQuestions = $allQuestions->merge($questions);
            }

            $department->setRelation('questions', $allQuestions);
        });

        return $departments->filter(fn ($dept) => $dept->questions->count() > 0);
    }

    /**
     * Initialize or retrieve an existing PENDING attempt for a specific Test.
     */
    public function initializeTestAttemptForNewTest(
        Supervisor $supervisor,
        Test $test,
        \Illuminate\Support\Collection $departments
    ): SupervisorTestAttempt {
        // Check for an existing pending attempt for THIS specific test
        $existingAttempt = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)
            ->where('test_id', $test->id)
            ->where('status', 'pending')
            ->first();

        if ($existingAttempt) {
            $existingAttempt->load('attemptedQuestions.attemptedAnswers');
            return $existingAttempt;
        }

        return DB::transaction(function () use ($supervisor, $test, $departments) {

            $attempt = SupervisorTestAttempt::create([
                'supervisor_id'           => $supervisor->id,
                'test_id'                 => $test->id,
                'test_name'               => $test->name,
                'fees'                    => $test->fees,
                'question_per_department' => $test->question_per_department,
                'timing'                  => $test->timing,
                'no_of_departments'       => $test->no_of_departments,
                'total_question'          => $test->total_question,
                // per_question_marks is not a single value anymore — we store 0
                // and calculate per-question using the attempted question's type.
                // We keep the column for backward compat but scoring is type-aware.
                'per_question_marks'      => 0,
                'total_marks'             => $test->total_marks,
                'passing_marks'           => $test->passing_marks,
                'start_time'              => now(),
                'end_time'                => now()->addMinutes($test->timing),
                'test_submission_status'  => 'pending',
                'status'                  => 'pending',
            ]);

            $typeConfig = $test->getQuestionTypeConfig();

            foreach ($departments as $department) {
                foreach ($department->questions as $question) {

                    // Determine effective answer type for display/saving
                    $effectiveAnswerType = in_array($question->answer_type, ['image', 'video'])
                        ? $question->sub_answer_type  // how the answer is shown
                        : $question->answer_type;

                    // Marks for this question type
                    $marksForType = $typeConfig[$question->answer_type]['marks'] ?? 0;

                    $attemptedQuestion = SupervisorAttemptedQuestion::create([
                        'supervisor_test_attempt_id' => $attempt->id,
                        'question_id'                => $question->id,
                        'question'                   => $question->question,
                        'department'                 => $department->name,
                        'answer_type'                => $effectiveAnswerType,  // what UI shows
                        'is_attempted'               => 0,
                        'is_corrected_by_user'       => 0,
                        // Store marks per question on the attempted question row
                        // so scoring works correctly even if test config changes later.
                        // NOTE: we add this column in the migration below.
                        // If column doesn't exist yet, this will be ignored gracefully
                        // by Laravel (or you can comment it out until migration runs).
                        'marks_per_question'         => $marksForType,
                    ]);

                    foreach ($question->answers as $answer) {
                        SupervisorAttemptedAnswer::create([
                            'supervisor_attempted_question_id' => $attemptedQuestion->id,
                            'answer_id'                        => $answer->id,
                            'answer'                           => $answer->answer,
                            'is_correct'                       => $answer->is_correct,
                            'user_answer'                      => null,
                        ]);
                    }
                }
            }

            return $attempt;
        });
    }

    /**
     * Submit a test attempt (new test-aware version).
     * Scoring is now per-question-type-marks aware.
     */
    public function submitTestForNewTest(int $attemptId, string $submissionType = 'supervisor'): SupervisorTestAttempt
    {
        return DB::transaction(function () use ($attemptId, $submissionType) {

            $attempt = SupervisorTestAttempt::with('attemptedQuestions')->findOrFail($attemptId);

            // Calculate score using per-question marks stored on each attempted question
            $scoreObtained = 0;
            foreach ($attempt->attemptedQuestions as $aq) {
                if ($aq->is_corrected_by_user) {
                    $marksPerQ = $aq->marks_per_question ?? $attempt->per_question_marks;
                    $scoreObtained += $marksPerQ;
                }
            }

            $percentage = $attempt->total_marks > 0
                ? ($scoreObtained / $attempt->total_marks) * 100
                : 0;

            $attempt->update([
                'user_taken_time'        => now()->diffInSeconds($attempt->start_time),
                'test_submission_status' => $submissionType,
                'status'                 => 'underReview',
                'score_obtained'         => $scoreObtained,
                'percentage'             => round($percentage, 2),
                'submitted_at'           => now(),
            ]);

            return $attempt;
        });
    }

    // =========================================================================
    //  LEGACY: TestSetting-based methods (kept intact, unchanged)
    // =========================================================================

    /**
     * @deprecated Use getTestWithQuestionsForNewTest() for new Test model.
     */
    public function getTestWithQuestions(TestSetting $test)
    {
        $departments = Department::latest()
            ->limit($test->no_of_departments)
            ->get();

        $departments->each(function ($department) use ($test) {
            $department->setRelation(
                'questions',
                $department->questions()
                    ->where('status', 1)
                    ->with('answers')
                    ->inRandomOrder()
                    ->limit($test->question_per_department)
                    ->get()
            );
        });

        return $departments->filter(fn ($dept) => $dept->questions->count() > 0);
    }

    /**
     * @deprecated Use initializeTestAttemptForNewTest() for new Test model.
     */
    public function initializeTestAttempt(Supervisor $supervisor, TestSetting $test, $departments)
    {
        $existingAttempt = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)
            ->where('status', 'pending')
            ->whereNull('test_id')   // legacy attempts have no test_id
            ->first();

        if ($existingAttempt) {
            $existingAttempt->load('attemptedQuestions.attemptedAnswers');
            return $existingAttempt;
        }

        return DB::transaction(function () use ($supervisor, $test, $departments) {

            $attempt = SupervisorTestAttempt::create([
                'supervisor_id'           => $supervisor->id,
                'test_id'                 => null,
                'test_name'               => $test->name,
                'fees'                    => $test->fees,
                'question_per_department' => $test->question_per_department,
                'timing'                  => $test->timing,
                'no_of_departments'       => $test->no_of_departments,
                'total_question'          => $test->total_question,
                'per_question_marks'      => $test->marks,
                'total_marks'             => $test->total_marks,
                'passing_marks'           => $test->passing_marks,
                'start_time'              => now(),
                'end_time'                => now()->addMinutes($test->timing),
                'test_submission_status'  => 'pending',
                'status'                  => 'pending',
            ]);

            foreach ($departments as $department) {
                foreach ($department->questions as $question) {
                    $attemptedQuestion = SupervisorAttemptedQuestion::create([
                        'supervisor_test_attempt_id' => $attempt->id,
                        'question_id'                => $question->id,
                        'question'                   => $question->question,
                        'department'                 => $department->name,
                        'answer_type'                => $question->answer_type,
                        'is_attempted'               => 0,
                        'is_corrected_by_user'       => 0,
                        'marks_per_question'         => $test->marks,
                    ]);

                    foreach ($question->answers as $answer) {
                        SupervisorAttemptedAnswer::create([
                            'supervisor_attempted_question_id' => $attemptedQuestion->id,
                            'answer_id'                        => $answer->id,
                            'answer'                           => $answer->answer,
                            'is_correct'                       => $answer->is_correct,
                            'user_answer'                      => null,
                        ]);
                    }
                }
            }

            return $attempt;
        });
    }

    /**
     * Save user's answer — identical logic, works for both old and new.
     */
    public function saveAnswer(int $attemptId, $questionId, $answer): void
    {
        DB::transaction(function () use ($attemptId, $questionId, $answer) {

            $attemptedQuestion = SupervisorAttemptedQuestion::where('supervisor_test_attempt_id', $attemptId)
                ->where('question_id', $questionId)
                ->firstOrFail();

            $answerType = $attemptedQuestion->answer_type;

            $attemptedQuestion->update(['is_attempted' => 1]);

            if ($answerType === 'text') {
                $attemptedAnswer = $attemptedQuestion->attemptedAnswers()->first();
                $attemptedAnswer->update(['user_answer' => $answer]);
                $isCorrect = strcasecmp(trim($attemptedAnswer->answer), trim($answer)) === 0;
                $attemptedQuestion->update(['is_corrected_by_user' => $isCorrect ? 1 : 0]);

            } elseif ($answerType === 'single') {
                $attemptedAnswers = $attemptedQuestion->attemptedAnswers;
                foreach ($attemptedAnswers as $attemptedAnswer) {
                    $attemptedAnswer->update(['user_answer' => ($attemptedAnswer->answer_id == $answer) ? 1 : 0]);
                }
                $correctAnswer = $attemptedAnswers->where('is_correct', 1)->first();
                $isCorrect     = $correctAnswer && $correctAnswer->answer_id == $answer;
                $attemptedQuestion->update(['is_corrected_by_user' => $isCorrect ? 1 : 0]);

            } elseif ($answerType === 'multi') {
                $answer          = is_array($answer) ? $answer : [$answer];
                $attemptedAnswers = $attemptedQuestion->attemptedAnswers;
                foreach ($attemptedAnswers as $attemptedAnswer) {
                    $attemptedAnswer->update([
                        'user_answer' => in_array($attemptedAnswer->answer_id, $answer) ? 1 : 0,
                    ]);
                }
                $correctIds = $attemptedAnswers->where('is_correct', 1)->pluck('answer_id')->toArray();
                $isCorrect  = count(array_diff($correctIds, $answer)) === 0
                           && count(array_diff($answer, $correctIds)) === 0;
                $attemptedQuestion->update(['is_corrected_by_user' => $isCorrect ? 1 : 0]);
            }
        });
    }

    /**
     * Legacy submit (TestSetting path).
     * @deprecated Use submitTestForNewTest() for new Test model.
     */
    public function submitTest(int $attemptId, string $submissionType = 'supervisor'): SupervisorTestAttempt
    {
        return DB::transaction(function () use ($attemptId, $submissionType) {

            $attempt       = SupervisorTestAttempt::with('attemptedQuestions')->findOrFail($attemptId);
            $totalCorrect  = $attempt->attemptedQuestions->where('is_corrected_by_user', 1)->count();
            $scoreObtained = $totalCorrect * $attempt->per_question_marks;
            $percentage    = $attempt->total_marks > 0
                ? ($scoreObtained / $attempt->total_marks) * 100
                : 0;

            $attempt->update([
                'user_taken_time'        => now()->diffInSeconds($attempt->start_time),
                'test_submission_status' => $submissionType,
                'status'                 => 'underReview',
                'score_obtained'         => $scoreObtained,
                'percentage'             => round($percentage, 2),
                'submitted_at'           => now(),
            ]);

            return $attempt;
        });
    }

    // =========================================================================
    //  Shared helpers
    // =========================================================================

    public function getRemainingTime(int $attemptId): int
    {
        $attempt = SupervisorTestAttempt::findOrFail($attemptId);
        $now     = Carbon::now();
        $endTime = Carbon::parse($attempt->end_time);

        return $now->greaterThanOrEqualTo($endTime) ? 0 : $now->diffInSeconds($endTime);
    }

    public function isTestExpired(int $attemptId): bool
    {
        return $this->getRemainingTime($attemptId) <= 0;
    }
}