<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TestService;
use App\Models\SupervisorTestAttempt;
use App\Models\TestSecurityLog;
use App\Models\TestTabSwitch;
use App\Models\TestCameraActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    protected TestService $testService;

    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }

    public function logCameraActivity(Request $request)
    {
        $request->validate([
            'attempt_id'    => 'required|exists:supervisor_test_attempts,id',
            'activity_type' => 'required|string',
        ]);

        try {
            TestCameraActivity::create([
                'supervisor_test_attempt_id' => $request->attempt_id,
                'activity_type'             => $request->activity_type,
                'details'                   => $request->details,
                'occurred_at'               => now(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function saveAnswer(Request $request)
    {
        $request->validate([
            'attempt_id'  => 'required|exists:supervisor_test_attempts,id',
            'question_id' => 'required',
            'answer'      => 'required',
        ]);

        try {
            $attempt = SupervisorTestAttempt::where('id', $request->attempt_id)
                ->where('supervisor_id', Auth::user()->supervisorDetail->id)
                ->where('status', 'pending')
                ->firstOrFail();

            if ($this->testService->isTestExpired($attempt->id)) {
                return response()->json(['success' => false, 'message' => 'Test time expired', 'expired' => true], 400);
            }

            $this->testService->saveAnswer($request->attempt_id, $request->question_id, $request->answer);

            $attemptedCount = $attempt->attemptedQuestions()->where('is_attempted', 1)->count();

            return response()->json(['success' => true, 'message' => 'Answer saved', 'attempted_count' => $attemptedCount]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save answer: ' . $e->getMessage()], 500);
        }
    }

    public function getRemainingTime(Request $request)
    {
        $request->validate(['attempt_id' => 'required|exists:supervisor_test_attempts,id']);

        try {
            $remainingSeconds = $this->testService->getRemainingTime($request->attempt_id);
            return response()->json(['success' => true, 'remaining_seconds' => $remainingSeconds, 'expired' => $remainingSeconds <= 0]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to get time'], 500);
        }
    }

    public function submitTest(Request $request)
    {
        $request->validate(['attempt_id' => 'required|exists:supervisor_test_attempts,id']);

        try {
            $attempt = SupervisorTestAttempt::where('id', $request->attempt_id)
                ->where('supervisor_id', Auth::user()->supervisorDetail->id)
                ->where('status', 'pending')
                ->firstOrFail();

            // Accept submission_type from request — 'supervisor' or 'automatic'
            // Default to 'supervisor' if not provided
            $submissionType = $request->input('submission_type', 'supervisor');

            // Validate submission_type value
            if (!in_array($submissionType, ['supervisor', 'automatic'])) {
                $submissionType = 'supervisor';
            }

            // Use new scoring if attempt has a test_id (new multi-test system)
            if ($attempt->test_id) {
                $submitted = $this->testService->submitTestForNewTest($attempt->id, $submissionType);
            } else {
                $submitted = $this->testService->submitTest($attempt->id, $submissionType);
            }

            session()->flash('success', 'Test submitted successfully.');

            return response()->json([
                'success'     => true,
                'message'     => 'Test submitted successfully',
                'status'      => $submitted->status,
                'score'       => $submitted->score_obtained,
                'total'       => $submitted->total_marks,
                'percentage'  => $submitted->percentage,
                'test_id'     => $submitted->test_id,
                'submit_type' => $submissionType,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to submit test: ' . $e->getMessage()], 500);
        }
    }

    public function logViolation(Request $request)
    {
        $request->validate([
            'attempt_id'     => 'required|exists:supervisor_test_attempts,id',
            'violation_type' => 'required|string',
        ]);

        try {
            Log::channel('security')->warning('Test Security Violation', [
                'supervisor_test_attempt_id' => $request->attempt_id,
                'violation_type'             => $request->violation_type,
                'violation_data'             => $request->violation_data,
                'timestamp'                  => $request->timestamp,
                'ip_address'                 => $request->ip(),
                'user_agent'                 => $request->userAgent(),
            ]);

            TestSecurityLog::create([
                'supervisor_test_attempt_id' => $request->attempt_id,
                'violation_type'             => $request->violation_type,
                'violation_data'             => $request->violation_data,
                'ip_address'                 => $request->ip(),
                'user_agent'                 => $request->userAgent(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function logTabSwitch(Request $request)
    {
        $request->validate([
            'attempt_id'  => 'required|exists:supervisor_test_attempts,id',
            'switched_at' => 'nullable|string',
        ]);

        try {
            // Use server's now() to ensure consistent timezone with submission time
            $switchedAt = now();

            $attempt = SupervisorTestAttempt::with('tabSwitches')->findOrFail($request->attempt_id);
            
            // Ignore if already over limit or test not pending
            if ($attempt->status !== 'pending' || $attempt->tabSwitches->count() > SupervisorTestAttempt::MAX_TAB_SWITCHES) {
                return response()->json(['success' => true, 'ignored' => true]);
            }

            TestTabSwitch::create([
                'supervisor_test_attempt_id' => $request->attempt_id,
                'switched_at'               => $switchedAt,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}