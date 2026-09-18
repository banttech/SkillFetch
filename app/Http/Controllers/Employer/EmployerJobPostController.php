<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostJob;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\JobTitle;
use App\Models\EmployerSkill;
use App\Models\WorkLocation;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class EmployerJobPostController extends Controller
{
    public function index()
    {
        try {
            // Get logged-in employer_details ID
            $employerId = Auth::user()->employerDetail->id;

            $jobs = PostJob::with(['skills', 'experiences','appliedSupervisors'])
                ->where('employer_id', $employerId)
                ->orderBy('id', 'DESC')
                ->paginate(10);
            
            $pageTitle = 'My Jobs';

            return view('employer.postjob.index', compact('jobs', 'pageTitle'));
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to load jobs: ' . $e->getMessage());
        }
    }


    public function create()
    {
        $skills = Skill::all();
        $experiences = Experience::all();
        $jobTitles = JobTitle::orderBy('id', 'desc')->get();
        $employerId = Auth::user()->employerDetail->id;
        $employerSkills = EmployerSkill::where('employer_id', $employerId)->get();
        $workLocations = WorkLocation::all();
        $pageTitle = 'Post New Job';
        
        return view('employer.postjob.create', compact('pageTitle', 'skills', 'experiences', 'jobTitles', 'employerSkills', 'workLocations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_title_id' => 'required|exists:job_titles,id',
            'description' => 'required',
            'skills' => 'required|array',
            'employer_skills' => 'required|array',
            'work_locations' => 'required|array',
            'experience_details' => 'required|array',
            'years' => 'required',
            'status' => 'required|in:0,1',
            'fixed_salary' => 'required|integer|min:0',
            'variable_salary_from' => 'required|integer|min:0',
            'variable_salary_to' => 'required|integer|min:0|gte:variable_salary_from',
            'petrol_allowance' => 'required|integer|min:0',
            'accommodation' => 'required|integer|min:0',
            'food_allowance' => 'required|integer|min:0',
        ], [
            'job_title_id.required' => 'Job Title field is required.',
            'description.required' => 'Description field is required.',
            'skills.required' => 'Please select at least one Admin Skill.',
            'employer_skills.required' => 'Please select at least one Employer Skill.',
            'work_locations.required' => 'Please select at least one Work Location.',
            'experience_details.required' => 'Please select at least one experience.',
            'years.required' => 'Total Years of Experience field is required.',
            'status.required' => 'Status field is required.',
            'fixed_salary.required' => 'Fixed Salary field is required.',
            'variable_salary_from.required' => 'Variable Salary From field is required.',
            'variable_salary_to.required' => 'Variable Salary To field is required.',
            'petrol_allowance.required' => 'Petrol Allowance field is required.',
            'accommodation.required' => 'Accommodation field is required.',
            'food_allowance.required' => 'Food Allowance field is required.',
            'variable_salary_to.gte' => 'Variable Salary From cannot be greater than To.',
        ]);

        try {
            DB::beginTransaction();
            // Get employer_details.id
            $employerId = Auth::user()->employerDetail->id;
            $jobTitleModel = JobTitle::find($request->job_title_id);

            // Create Job
            $job = PostJob::create([
                'employer_id' => $employerId,
                'job_title_id' => $request->job_title_id,
                'title' => $jobTitleModel ? $jobTitleModel->title : '',
                'description' => $request->description,
                'years' => $request->years,
                'status' => $request->status,
                'paymentStatus' => 'unPaid',
                'fixed_salary' => $request->fixed_salary,
                'variable_salary_from' => $request->variable_salary_from,
                'variable_salary_to' => $request->variable_salary_to,
                'petrol_allowance' => $request->petrol_allowance,
                'accommodation' => $request->accommodation,
                'food_allowance' => $request->food_allowance,
            ]);

            $job->skills()->sync($request->skills);
            $job->experiences()->sync($request->experience_details);
            
            if ($request->has('employer_skills')) {
                $job->employerSkills()->sync($request->employer_skills);
            }
            if ($request->has('work_locations')) {
                $job->workLocations()->sync($request->work_locations);
            }

            DB::commit();

            return redirect()->route('employer.myjobs')
                ->with('success', 'Job Posted Successfully!');
        } catch (\Exception $e) {
            Log::error("Job Posting Error: " . $e->getMessage());
            return back()->with('error', 'Failed to post job: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        try {
            $employerId = Auth::user()->employerDetail->id;
            $pageTitle = 'Update Job';
            $job = PostJob::where('id', $id)->with(['skills', 'experiences', 'employerSkills', 'workLocations'])
                ->where('employer_id', $employerId)
                ->firstOrFail();

            $skills = Skill::all();
            $experiences = Experience::all();
            $jobTitles = JobTitle::orderBy('id', 'desc')->get();
            $employerSkills = EmployerSkill::where('employer_id', $employerId)->get();
            $workLocations = WorkLocation::all();
            
            $isLocked = $job->appliedSupervisors()->exists();

            return view('employer.postjob.edit', compact('job', 'skills', 'experiences', 'jobTitles', 'employerSkills', 'workLocations', 'pageTitle', 'isLocked'));
        } catch (\Exception $e) {
            return redirect()->route('employer.myjobs')
                ->with('error', 'Unable to load job: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $employerId = Auth::user()->employerDetail->id;
            $job = PostJob::where('id', $id)->where('employer_id', $employerId)->firstOrFail();
            
            if ($job->appliedSupervisors()->exists()) {
                return back()->with('error', 'Your job is already published and has applicants, so you cannot edit it.');
            }

            $request->validate([
                'job_title_id' => 'required|exists:job_titles,id',
                'description' => 'required',
                'skills' => 'required|array',
                'employer_skills' => 'required|array',
                'work_locations' => 'required|array',
                'experience_details' => 'required|array',
                'years' => 'required',
                'status' => 'required|in:0,1',
                'fixed_salary' => 'required|integer|min:0',
                'variable_salary_from' => 'required|integer|min:0',
                'variable_salary_to' => 'required|integer|min:0|gte:variable_salary_from',
                'petrol_allowance' => 'required|integer|min:0',
                'accommodation' => 'required|integer|min:0',
                'food_allowance' => 'required|integer|min:0',
            ], [
                'job_title_id.required' => 'Job Title field is required.',
                'description.required' => 'Description field is required.',
                'skills.required' => 'Please select at least one Admin Skill.',
                'employer_skills.required' => 'Please select at least one Employer Skill.',
                'work_locations.required' => 'Please select at least one Work Location.',
                'experience_details.required' => 'Please select at least one experience.',
                'years.required' => 'Total Years of Experience field is required.',
                'status.required' => 'Status field is required.',
                'fixed_salary.required' => 'Fixed Salary field is required.',
                'variable_salary_from.required' => 'Variable Salary From field is required.',
                'variable_salary_to.required' => 'Variable Salary To field is required.',
                'petrol_allowance.required' => 'Petrol Allowance field is required.',
                'accommodation.required' => 'Accommodation field is required.',
                'food_allowance.required' => 'Food Allowance field is required.',
                'variable_salary_to.gte' => 'Variable Salary From cannot be greater than To.',
            ]);

            DB::beginTransaction();

            $jobTitleModel = JobTitle::find($request->job_title_id);

            $job->update([
                'job_title_id' => $request->job_title_id,
                'title' => $jobTitleModel ? $jobTitleModel->title : '',
                'description' => $request->description,
                'years' => $request->years,
                'status' => $request->status,
                'fixed_salary' => $request->fixed_salary,
                'variable_salary_from' => $request->variable_salary_from,
                'variable_salary_to' => $request->variable_salary_to,
                'petrol_allowance' => $request->petrol_allowance,
                'accommodation' => $request->accommodation,
                'food_allowance' => $request->food_allowance,
            ]);

            $job->skills()->sync($request->skills);
            $job->experiences()->sync($request->experience_details);

            if ($request->has('employer_skills')) {
                $job->employerSkills()->sync($request->employer_skills);
            } else {
                $job->employerSkills()->sync([]);
            }

            if ($request->has('work_locations')) {
                $job->workLocations()->sync($request->work_locations);
            } else {
                $job->workLocations()->sync([]);
            }

            DB::commit();

            return redirect()->route('employer.myjobs')
                ->with('success', 'Job Updated Successfully!');
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error("Job Update Error: " . $e->getMessage());
            return back()->with('error', 'Failed to update job: ' . $e->getMessage());
        }
    }


    public function details($id)
    {
        try {
            $employerId = Auth::user()->employerDetail->id;

            $pageTitle = 'Job Detail';

            $job = PostJob::where('id', $id)->with(['skills', 'experiences', 'employerSkills', 'workLocations', 'appliedSupervisors.user'])
                ->where('employer_id', $employerId)
                ->firstOrFail();

            return view('employer.postjob.job-details', compact('job', 'pageTitle'));
        } catch (\Exception $e) {
            return redirect()->route('employer.myjobs')
                ->with('error', 'Unable to load Job Detail: ' . $e->getMessage());
        }
    }
    
    public function exportAppliedSupervisors($id)
    {
        try {
            $employerId = Auth::user()->employerDetail->id;

            $job = PostJob::where('id', $id)->with(['appliedSupervisors.user', 'appliedSupervisors.state', 'appliedSupervisors.skills', 'appliedSupervisors.experiences', 'appliedSupervisors.workLocations'])
                ->where('employer_id', $employerId)
                ->firstOrFail();

            $supervisors = $job->appliedSupervisors;

            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=applied_supervisors_job_{$id}.csv",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );
            
            $columns = [
                'Name', 'Email', 'Phone', 'Address', 'City', 'State', 'Pincode', 
                'Own a Bike', 'Own a Phone', 'Skills', 'Experience Areas', 'Preferred Work Locations'
            ];

            $callback = function() use($supervisors, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($supervisors as $sup) {
                    $skills = $sup->skills ? $sup->skills->pluck('name')->implode(', ') : 'N/A';
                    $experiences = $sup->experiences ? $sup->experiences->pluck('name')->implode(', ') : 'N/A';
                    $workLocations = $sup->workLocations ? $sup->workLocations->pluck('name')->implode(', ') : 'N/A';
                    $state = $sup->state ? $sup->state->name : 'N/A';

                    $row = [
                        $sup->user->name ?? 'N/A',
                        $sup->user->email ?? 'N/A',
                        $sup->user->phone ?? 'N/A',
                        $sup->address ?? 'N/A',
                        $sup->city ?? 'N/A',
                        $state,
                        $sup->pincode ?? 'N/A',
                        $sup->own_bike ? 'Yes' : 'No',
                        $sup->own_phone ? 'Yes' : 'No',
                        $skills,
                        $experiences,
                        $workLocations
                    ];

                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->route('employer.job.details', ['id' => $id])
                ->with('error', 'Unable to export supervisors: ' . $e->getMessage());
        }
    }
    
    // AJAX Endpoint to store a Job Title directly
    public function storeJobTitle(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255|unique:job_titles,title']);
        $existing = JobTitle::where('title', $request->title)->first();
        if ($existing) {
            return response()->json(['success' => true, 'id' => $existing->id, 'text' => $existing->title]);
        }
        $jobTitle = JobTitle::create([
            'title' => $request->title,
            'created_by' => 'employer',
            'user_id' => Auth::id()
        ]);
        return response()->json(['success' => true, 'id' => $jobTitle->id, 'text' => $jobTitle->title]);
    }

    // AJAX Endpoint to fetch Job Titles
    public function getJobTitles(Request $request)
    {
        $query = JobTitle::query();
        if ($request->has('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }
        $titles = $query->orderBy('id','desc')->get()->map(function($t) { return ['id' => $t->id, 'text' => $t->title]; });
        return response()->json($titles);
    }

    public function paymentInitiate(Request $request)
    {
        try {
            $user = Auth::user();
            $employerId = $user->employerDetail->id;
            $jobId = $request->input('job_id');
            
            if (!$jobId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job Id field is required.'
                ], 404);
            }

            $job = PostJob::where('id', $jobId)
                ->where('employer_id', $employerId)
                ->where('paymentStatus', 'unPaid')
                ->first();
                
            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found for this employer.'
                ], 404);
            }

            // Check if order_id already exists and is still valid
            if ($job->payment_order_id) {
                try {
                    // Razorpay Instance
                    $api = new Api(
                        config('services.razorpay.key'),
                        config('services.razorpay.secret')
                    );

                    // Fetch existing order from Razorpay
                    $existingOrder = $api->order->fetch($job->payment_order_id);

                    // If order status is 'created' (not paid/expired), reuse it
                    if ($existingOrder->status === 'created') {
                        return response()->json([
                            'success' => true,
                            'orderId' => $job->payment_order_id,
                            'amount' => 200,
                            'key' => config('services.razorpay.key'),
                            'name' => $user->name,
                            'email' => $user->email
                        ]);
                    }
                    
                    // If order is paid/attempted/expired, we'll create a new one below
                    
                } catch (\Exception $e) {
                    // Order fetch failed (might be invalid/expired), create new one
                    Log::warning("Existing order fetch failed for job {$jobId}: " . $e->getMessage());
                }
            }

            // Create NEW Razorpay Order only if no valid existing order
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $razorpayOrder = $api->order->create([
                'receipt' => "JOB-" . $job->id . "-" . time(),
                'amount' => 200 * 100,  // amount in paise
                'currency' => 'INR',
                'notes' => [
                    'payment_type' => 'employer_job_posting',
                    'job_id' => $job->id,
                    'employer_id' => $employerId,
                ]
            ]);

            if (!$razorpayOrder || !isset($razorpayOrder['id'])) {
                throw new Exception("Failed to create Razorpay order.");
            }

            // Save new order_id
            $job->payment_order_id = $razorpayOrder['id'];
            $job->save();

            // Return response for Razorpay Checkout
            return response()->json([
                'success' => true,
                'orderId' => $razorpayOrder['id'],
                'amount' => 200,
                'key' => config('services.razorpay.key'),
                'name' => $user->name,
                'email' => $user->email
            ]);
            
        } catch (\Exception $e) {
            Log::error("Payment Initiation Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Payment Initiation Error: " . $e->getMessage(),
            ], 500);
        }
    }
}
