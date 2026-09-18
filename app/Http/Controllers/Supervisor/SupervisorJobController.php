<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\PostJob;
use App\Models\Supervisor;
use App\Models\SupervisorAppliedJob;
use App\Traits\SupervisorJobValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupervisorJobController extends Controller
{
    use SupervisorJobValidation;

    /**
     * Open Jobs Listing
     */
    public function openJobs()
    {
        $pageTitle = 'Open Jobs';

        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        if (!$supervisor->passedTestAttempt()->exists()) {
            return redirect()
                ->route('supervisor.dashboard')
                ->with('error', 'You must pass the qualification test to view open jobs.');
        }

        // Get supervisor's skills and experiences
        $supervisorData = $this->getSupervisorSkillsAndExperiences($supervisor);

        // Get applied job IDs to exclude them
        $appliedJobIds = $supervisor->appliedJobs()->pluck('job_id')->toArray();

        // Fetch only Active + Paid jobs that match supervisor's profile
        $jobs = PostJob::with(['skills', 'experiences'])
            ->where('status', 1)
            ->where('paymentStatus', 'paid')
            ->whereNotIn('id', $appliedJobIds); // Exclude applied jobs

        // Apply matching filter
        $jobs = $this->applyJobMatchingFilter(
            $jobs,
            $supervisorData['skills'],
            $supervisorData['experiences']
        );

        $jobs = $jobs->orderBy('id', 'DESC')->paginate(10);

        return view('supervisor.jobs.openjobs', compact('jobs', 'pageTitle', 'supervisor'));
    }

    /**
     * Job Detail Page (for both open and applied jobs)
     */
    public function jobDetail($id)
    {
        $pageTitle = 'Job Detail';

        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        if (!$supervisor->passedTestAttempt()->exists()) {
            return redirect()
                ->route('supervisor.dashboard')
                ->with('error', 'You must pass the qualification test to view jobs.');
        }

        // Validate job for supervisor
        $job = $this->validateJobForSupervisor($id, $supervisor, true);

        if (!$job) {
            return redirect()
                ->route('supervisor.openjobs')
                ->with('error', 'This job is not available or does not match your profile.');
        }

        // Get supervisor's skills and experiences
        $supervisorData = $this->getSupervisorSkillsAndExperiences($supervisor);

        // Get matching details
        $matchDetails = $this->getJobMatchDetails(
            $job,
            $supervisorData['skills'],
            $supervisorData['experiences']
        );

        // Check if already applied
        $hasApplied = $supervisor->appliedJobs()->where('job_id', $job->id)->exists();

        return view('supervisor.jobs.job-detail', array_merge(
            compact('job', 'pageTitle', 'supervisor', 'hasApplied'),
            $matchDetails
        ));
    }

    /**
     * Apply for Job
     */
    public function applyJob(Request $request, $id)
    {
        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        if (!$supervisor->passedTestAttempt()->exists()) {
            return redirect()
                ->route('supervisor.dashboard')
                ->with('error', 'You must pass the qualification test to apply for jobs.');
        }

        // Validate job for supervisor
        $job = $this->validateJobForSupervisor($id, $supervisor, true);

        if (!$job) {
            return redirect()
                ->route('supervisor.openjobs')
                ->with('error', 'This job is not available or does not match your profile.');
        }

        // Check if already applied
        $alreadyApplied = $supervisor->appliedJobs()->where('job_id', $job->id)->exists();

        if ($alreadyApplied) {
            return redirect()
                ->back()
                ->with('error', 'You have already applied for this job.');
        }

        // Create application
        try {
            DB::beginTransaction();

            SupervisorAppliedJob::create([
                'supervisor_id' => $supervisor->id,
                'job_id' => $job->id,
                'status' => 'pending',
                'applied_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('supervisor.appliedJobs')
                ->with('success', 'You have successfully applied for the job!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Failed to apply for the job. Please try again.');
        }
    }

    /**
     * Applied Jobs Listing
     */
    public function appliedJobs()
    {
        $pageTitle = 'Applied Jobs';

        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        if (!$supervisor->passedTestAttempt()->exists()) {
            return redirect()
                ->route('supervisor.dashboard')
                ->with('error', 'You must pass the qualification test to view applied jobs.');
        }

        // Get applied jobs with their details
        $appliedJobs = SupervisorAppliedJob::with(['job.skills', 'job.experiences', 'job.employer.user'])
            ->where('supervisor_id', $supervisor->id)
            ->orderBy('applied_at', 'DESC')
            ->paginate(10);

        return view('supervisor.jobs.applied-jobs', compact('appliedJobs', 'pageTitle', 'supervisor'));
    }
}