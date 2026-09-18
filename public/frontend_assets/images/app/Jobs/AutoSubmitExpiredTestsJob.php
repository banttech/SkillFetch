<?php

namespace App\Jobs;

use App\Models\SupervisorTestAttempt;
use App\Services\TestService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoSubmitExpiredTestsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Find all expired tests that are still pending
        $expiredAttempts = SupervisorTestAttempt::where('status', 'pending')
            ->where('end_time', '<', Carbon::now())
            ->get();

        if ($expiredAttempts->isEmpty()) {
            Log::info('No expired tests found');
            return;
        }

        $testService = new TestService();

        foreach ($expiredAttempts as $attempt) {
            try {
                $testService->submitTest($attempt->id, 'automatic');
                Log::info("Auto-submitted expired test for attempt ID: {$attempt->id}");
            } catch (\Exception $e) {
                Log::error("Failed to auto-submit test {$attempt->id}: " . $e->getMessage());
            }
        }

        Log::info("Auto-submitted {$expiredAttempts->count()} expired tests");
    }
}