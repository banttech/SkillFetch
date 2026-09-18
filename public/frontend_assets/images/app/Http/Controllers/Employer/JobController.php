<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostJob;
use App\Models\EmployerJobPayment;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\EmployerJobSkill;
use App\Models\EmployerJobExperience;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function create()
    {
        return view('employer.postjob.post-job', [
            'page_title' => 'Post New Job',
            'skills' => Skill::all(),
            'experiences' => Experience::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'skills' => 'nullable|array',
            'experience_details' => 'nullable|array',
            'years' => 'required',
            'status' => 'required|in:Active,Inactive',
        ]);

        try {

            // Get employer_details.id
            $employerId = Auth::user()->employerDetail->id;

            // Create Job
            $job = PostJob::create([
                'employer_id' => $employerId,
                'title' => $request->title,
                'description' => $request->description,
                'years' => $request->years,
                'status' => $request->status,
            ]);

            // Insert SKILLS pivot
            if ($request->skills) {
                foreach ($request->skills as $skillId) {
                    EmployerJobSkill::create([
                        'job_id' => $job->id,
                        'skill_id' => $skillId,
                        'employer_id' => $employerId,
                    ]);
                }
            }

            // Insert EXPERIENCE pivot
            if ($request->experience_details) {
                foreach ($request->experience_details as $expId) {
                    EmployerJobExperience::create([
                        'job_id' => $job->id,
                        'experience_id' => $expId,
                        'employer_id' => $employerId,
                    ]);
                }
            }

            // Create Payment Record
            EmployerJobPayment::create([
                'job_id' => $job->id,
                'payment_status' => 'unpaid',
                'transaction_id' => null,
                'payment_order_id' => null
            ]);

            return redirect()->route('employer.myjobs')
                ->with('success', 'Job Posted Successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to post job: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        try {
            $employerId = Auth::user()->employerDetail->id;

            $job = PostJob::where('id', $id)
                ->where('employer_id', $employerId)
                ->firstOrFail();

            $skills = Skill::all();
            $experiences = Experience::all();

            // Fetch selected IDs using model relations
            $selectedSkills = $job->skills()->pluck('skill_id')->toArray();
            $selectedExperiences = $job->experiences()->pluck('experience_id')->toArray();

            return view('employer.postjob.edit-job', [
                'job' => $job,
                'skills' => $skills,
                'experiences' => $experiences,
                'selectedSkills' => $selectedSkills,
                'selectedExperiences' => $selectedExperiences,
                'page_title' => 'Edit Job'
            ]);

        } catch (\Exception $e) {
            return redirect()->route('employer.myjobs')
                ->with('error', 'Unable to load job: ' . $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'skills' => 'nullable|array',
            'experience_details' => 'nullable|array',
            'years' => 'required',
            'status' => 'required|in:Active,Inactive',
        ]);

        try {

            $employerId = Auth::user()->employerDetail->id;

            $job = PostJob::where('id', $id)
                ->where('employer_id', $employerId)
                ->firstOrFail();

            // Update Job
            $job->update([
                'title' => $request->title,
                'description' => $request->description,
                'years' => $request->years,
                'status' => $request->status,
            ]);

            // Refresh skill pivot
            EmployerJobSkill::where('job_id', $job->id)->delete();
            if ($request->skills) {
                foreach ($request->skills as $skillId) {
                    EmployerJobSkill::create([
                        'job_id' => $job->id,
                        'skill_id' => $skillId,
                        'employer_id' => $employerId,
                    ]);
                }
            }

            // Refresh experience pivot
            EmployerJobExperience::where('job_id', $job->id)->delete();
            if ($request->experience_details) {
                foreach ($request->experience_details as $expId) {
                    EmployerJobExperience::create([
                        'job_id' => $job->id,
                        'experience_id' => $expId,
                        'employer_id' => $employerId,
                    ]);
                }
            }

            return redirect()->route('employer.myjobs')
                ->with('success', 'Job Updated Successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update job: ' . $e->getMessage());
        }
    }


    public function details($id)
    {
        try {
            $employerId = Auth::user()->employerDetail->id;

            $job = PostJob::where('id', $id)
                ->where('employer_id', $employerId)
                ->firstOrFail();

            return view('employer.postjob.job-details', [
                'job' => $job,
                'page_title' => 'Job Details'
            ]);

        } catch (\Exception $e) {
            return redirect()->route('employer.myjobs')
                ->with('error', 'Unable to load job details: ' . $e->getMessage());
        }
    }


    public function pay($id)
    {
        try {
            $employerId = Auth::user()->employerDetail->id;

            $job = PostJob::where('id', $id)
                ->where('employer_id', $employerId)
                ->firstOrFail();

            return "Redirect to payment page for Job ID: " . $id;

        } catch (\Exception $e) {
            return back()->with('error', 'Unable to process payment: ' . $e->getMessage());
        }
    }
}
