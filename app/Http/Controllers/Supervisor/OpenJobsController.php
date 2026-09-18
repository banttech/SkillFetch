<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostJob;
use App\Models\Supervisor;
use App\Models\SupervisorAppliedJob;
use Illuminate\Support\Facades\Auth;

class OpenJobsController extends Controller
{
    public function index()
    {
        $supervisorId = Supervisor::where('user_id', Auth::id())->value('id');

        // Fetch only Active + Paid jobs
        $jobs = PostJob::with(['payment', 'skills.skill', 'experiences.experience'])
            ->where('status', 'Active')
            ->whereHas('payment', function ($query) {
                $query->where('payment_status', 'paid');
            })
            ->paginate(10);

        return view('supervisor.openjobs.openjobs', [
            'pageTitle' => 'Open Jobs',
            'jobs' => $jobs,
            'supervisorId' => $supervisorId
        ]);
    }


    public function show($id)
    {
        $supervisorId = Supervisor::where('user_id', Auth::id())->value('id');

        $job = PostJob::with(['skills.skill', 'experiences.experience'])->findOrFail($id);

        // Check if already applied
        $alreadyApplied = SupervisorAppliedJob::where([
            'supervisor_id' => $supervisorId,
            'job_id' => $job->id
        ])->exists();

        return view('supervisor.openjobs.openjobsdetails', [
            'pageTitle' => 'Job Details',
            'job' => $job,
            'alreadyApplied' => $alreadyApplied
        ]);
    }


    // ================================
    // APPLY FOR JOB
    // ================================
    public function apply(Request $request)
    {
        $supervisorId = Supervisor::where('user_id', Auth::id())->value('id');

        if (!$supervisorId) {
            return back()->with('error', 'Supervisor profile not found.');
        }

        $jobId = $request->job_id;

        // Check duplicate apply
        $exists = SupervisorAppliedJob::where([
            'supervisor_id' => $supervisorId,
            'job_id' => $jobId
        ])->exists();

        if ($exists) {
            return back()->with('error', 'You have already applied for this job.');
        }

        // Insert apply record
        SupervisorAppliedJob::create([
            'supervisor_id' => $supervisorId,
            'job_id' => $jobId,
            'status' => 'applied'
        ]);

        return back()->with('success', 'Job applied successfully!');
    }

    public function appliedJobs()
    {
        $supervisorId = Supervisor::where('user_id', Auth::id())->value('id');

        $appliedJobs = SupervisorAppliedJob::with(['job.skills.skill', 'job.experiences.experience'])
            ->where('supervisor_id', $supervisorId)
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('supervisor.appliedjob.appliedjob', [
            'pageTitle' => 'Applied Jobs',
            'appliedJobs' => $appliedJobs
        ]);
    }

    public function appliedJobDetails($id)
    {
        $supervisorId = Supervisor::where('user_id', Auth::id())->value('id');

        $applied = SupervisorAppliedJob::with([
            'job.skills.skill',
            'job.experiences.experience'
        ])
            ->where('id', $id)
            ->where('supervisor_id', $supervisorId) // important fix
            ->firstOrFail();

        return view('supervisor.appliedjob.appliedjobdetail', [
            'pageTitle' => 'Applied Job Details',
            'applied' => $applied,
            'job' => $applied->job
        ]);
    }



}
