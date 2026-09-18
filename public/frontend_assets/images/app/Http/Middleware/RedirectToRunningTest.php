<?php

namespace App\Http\Middleware;

use App\Models\Supervisor;
use App\Models\SupervisorTestAttempt;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectToRunningTest
{
    /**
     * Routes that are allowed even when a test is running.
     * These are the test-taking routes themselves + AJAX endpoints.
     */
    private array $allowedRoutes = [
        'supervisor.tests.attempt',        // the test page itself
        'supervisor.test.save-answer',     // AJAX: save answer
        'supervisor.test.remaining-time',  // AJAX: get time
        'supervisor.test.submit',          // AJAX: submit
        'supervisor.test.log-violation',   // AJAX: log violation
        'supervisor.payment.verify',       // payment verify callback
        'logout',
    ];

    public function handle(Request $request, Closure $next)
    {
    

      $user = Auth::user();

        // Check if supervisor has a pending (running) test attempt
        $supervisor = Supervisor::where('user_id', $user->id)->first();

        if (!$supervisor) {
            return $next($request);
        }

        $runningAttempt = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)
            ->where('status', 'pending')
            ->first();

        if ($runningAttempt) {
            // Redirect to the running test
            return redirect()->route('supervisor.tests.attempt', $runningAttempt->test_id)
                ->with('warning', 'You have an ongoing test. Please complete it before accessing other pages.');
        }

        return $next($request);
    }
}