<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Supervisor;
use App\Models\SupervisorTestAttempt;
use App\Models\SupervisorTestFees;
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailableTestsController extends Controller
{
    protected TestService $testService;

    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }

    public function index()
    {
        $pageTitle   = 'Available Tests';
        $supervisor  = $this->getSupervisor();
        $globalBlock = SupervisorTestAttempt::getGlobalBlockForSupervisor($supervisor->id);

        $tests = Test::active()->orderBy('created_at', 'desc')->get();
        $tests->each(function (Test $test) use ($supervisor, $globalBlock) {
            $test->supervisor_status = $this->getTestStatus($supervisor, $test, $globalBlock);
        });

        return view('supervisor.tests.available-tests', compact('tests', 'pageTitle', 'supervisor'));
    }

    public function show(int $testId)
    {
        $pageTitle   = 'Test Details';
        $test        = Test::active()->findOrFail($testId);
        $supervisor  = $this->getSupervisor();
        $globalBlock = SupervisorTestAttempt::getGlobalBlockForSupervisor($supervisor->id);

        $attempts = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)
            ->where('test_id', $test->id)
            ->submitted()
            ->with(['testSkills.skill'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $status = $this->getTestStatus($supervisor, $test, $globalBlock);

        return view('supervisor.tests.test-detail', compact(
            'test', 'pageTitle', 'supervisor', 'attempts', 'status'
        ));
    }

    public function attempt(int $testId)
    {
        $pageTitle   = '';
        $test        = Test::active()->findOrFail($testId);
        $supervisor  = $this->getSupervisor();
        $globalBlock = SupervisorTestAttempt::getGlobalBlockForSupervisor($supervisor->id);
        $status      = $this->getTestStatus($supervisor, $test, $globalBlock);

        if (!$status['can_attempt']) {
            return redirect()->route('supervisor.tests.show', $testId)
                ->with('error', $status['block_reason'] ?? 'You cannot attempt this test right now.');
        }

        $departments = $this->testService->getTestWithQuestionsForNewTest($test);
        $attempt     = $this->testService->initializeTestAttemptForNewTest($supervisor, $test, $departments);

        if ($this->testService->isTestExpired($attempt->id)) {
            $this->testService->submitTestForNewTest($attempt->id, 'automatic');
            return redirect()->route('supervisor.tests.show', $testId)
                ->with('error', 'Test time expired and was submitted automatically.');
        }

        // Server-side Tab Switch Lockdown
        $attempt->load('tabSwitches');
        if ($attempt->tabSwitches->count() > SupervisorTestAttempt::MAX_TAB_SWITCHES) {
            $this->testService->submitTestForNewTest($attempt->id, 'automatic');
            return redirect()->route('supervisor.tests.show', $testId)
                ->with('error', 'Too many tab switches detected. Test submitted automatically.');
        }

        $attempt->load(['attemptedQuestions.attemptedAnswers', 'tabSwitches']);
        $groupedQuestions = $attempt->attemptedQuestions->groupBy('department');
        $attemptedCount   = $attempt->attemptedQuestions->where('is_attempted', 1)->count();
        $remainingSeconds = $this->testService->getRemainingTime($attempt->id);

        return view('supervisor.tests.take-test', compact(
            'test', 'attempt', 'groupedQuestions',
            'attemptedCount', 'remainingSeconds',
            'pageTitle', 'supervisor'
        ))->with('pageTitle', 'Test: ' . $test->name);
    }

    private function getSupervisor(): Supervisor
    {
        $supervisor = Supervisor::where('user_id', Auth::id())->first();
        if (!$supervisor) {
            abort(403, 'Supervisor profile not found.');
        }
        return $supervisor;
    }

    /**
     * State values:
     *   pending_in_progress  → THIS test is live right now
     *   under_review         → THIS test is under admin review
     *   wait_period          → THIS test just ended, wait period is active
     *   globally_locked      → ANOTHER test caused the block, this one is just locked
     *   not_paid             → free to attempt, just needs payment
     *   can_attempt          → paid and ready
     */
    public function getTestStatus(
        Supervisor $supervisor,
        Test $test,
        ?SupervisorTestAttempt $globalBlock
    ): array {

        // Step 1: Live pending attempt for THIS test
        $pendingAttempt = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)
            ->where('test_id', $test->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingAttempt) {
            return [
                'can_attempt'    => true,
                'state'          => 'pending_in_progress',
                'block_reason'   => null,
                'cooldown_ends'  => null,
                'blocking_test'  => null,
                'is_the_blocker' => false,
            ];
        }

        // Step 2: Global block exists — distinguish which test caused it
        if ($globalBlock) {
            $blockState   = $globalBlock->block_state;
            $isTheBlocker = ($globalBlock->test_id === $test->id);

            if ($blockState === 'under_review') {
                return $isTheBlocker
                    ? [
                        'can_attempt'    => false,
                        'state'          => 'under_review',
                        'block_reason'   => 'This test is currently under admin review.',
                        'cooldown_ends'  => null,
                        'blocking_test'  => $globalBlock->test_name,
                        'is_the_blocker' => true,
                    ]
                    : [
                        'can_attempt'    => false,
                        'state'          => 'globally_locked',
                        'block_reason'   => '"' . $globalBlock->test_name . '" is under admin review. All tests are locked until the result is published.',
                        'cooldown_ends'  => null,
                        'blocking_test'  => $globalBlock->test_name,
                        'is_the_blocker' => false,
                    ];
            }

            if ($blockState === 'cooldown') {
                return $isTheBlocker
                    ? [
                        'can_attempt'    => false,
                        'state'          => 'wait_period',
                        'block_reason'   => 'Please wait before reattempting this test.',
                        'cooldown_ends'  => $globalBlock->can_reattempt_after,
                        'blocking_test'  => $globalBlock->test_name,
                        'is_the_blocker' => true,
                    ]
                    : [
                        'can_attempt'    => false,
                        'state'          => 'globally_locked',
                        'block_reason'   => 'You recently attempted "' . $globalBlock->test_name . '". Wait until the wait period ends before attempting any test.',
                        'cooldown_ends'  => $globalBlock->can_reattempt_after,
                        'blocking_test'  => $globalBlock->test_name,
                        'is_the_blocker' => false,
                    ];
            }
        }

        // Step 3: Payment check for THIS test
        $lastAttemptForThisTest = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)
            ->where('test_id', $test->id)
            ->submitted()
            ->latest('submitted_at')
            ->first();

        $paymentQuery = SupervisorTestFees::where('supervisor_id', $supervisor->id)
            ->where('test_id', $test->id)
            ->where('payment_status', 'paid');

        if ($lastAttemptForThisTest) {
            $paymentQuery->where('created_at', '>', $lastAttemptForThisTest->submitted_at);
        }

        if (!$paymentQuery->latest()->first()) {
            return [
                'can_attempt'    => false,
                'state'          => 'not_paid',
                'block_reason'   => 'Please pay the test fee to attempt this test.',
                'cooldown_ends'  => null,
                'blocking_test'  => null,
                'is_the_blocker' => false,
            ];
        }

        return [
            'can_attempt'    => true,
            'state'          => 'can_attempt',
            'block_reason'   => null,
            'cooldown_ends'  => null,
            'blocking_test'  => null,
            'is_the_blocker' => false,
        ];
    }
}