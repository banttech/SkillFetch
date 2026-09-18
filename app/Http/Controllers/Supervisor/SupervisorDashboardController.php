<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Supervisor;
use App\Models\SupervisorTestAttempt;
use App\Services\TestService;
use Illuminate\Support\Facades\Auth;

class SupervisorDashboardController extends Controller
{
    protected TestService $testService;

    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }

    public function dashboard()
    {
        $pageTitle  = 'Supervisor Dashboard';
        $supervisor = Supervisor::where('user_id', Auth::id())->with('user')->first();

        if (!$supervisor) {
            return redirect()->route('login');
        }

        // Global block across ALL tests
        $globalBlock = SupervisorTestAttempt::getGlobalBlockForSupervisor($supervisor->id);

        // Has supervisor passed any test?
        $passedTest = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)
            ->where('status', 'pass')
            ->submitted()
            ->latest('submitted_at')
            ->first();

        // Summary stats
        $totalAttempts       = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)->submitted()->count();
        $passedCount         = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)->submitted()->where('status', 'pass')->count();
        $failedCount         = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)->submitted()->where('status', 'fail')->count();
        $availableTestsCount = Test::active()->count();

        // Active tests with per-test status (reuse AvailableTestsController logic)
        $testsController = new AvailableTestsController($this->testService);
        $tests = Test::active()->orderBy('created_at', 'desc')->get();
        $tests->each(function (Test $test) use ($supervisor, $globalBlock, $testsController) {
            $test->supervisor_status = $testsController->getTestStatus($supervisor, $test, $globalBlock);
        });

        // All submitted attempts newest-first
        $recentAttempts = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)
            ->submitted()
            ->with(['testSkills.skill'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        // Latest attempt per test (for last score display in test list)
        $recentAttemptsByTest = $recentAttempts
            ->groupBy('test_id')
            ->map(fn ($group) => $group->first());

        return view('supervisor.dashboard', compact(
            'pageTitle', 'supervisor',
            'globalBlock', 'passedTest',
            'totalAttempts', 'passedCount', 'failedCount', 'availableTestsCount',
            'tests', 'recentAttempts', 'recentAttemptsByTest'
        ));
    }
}