<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployerDetail;
use App\Models\Job;
use App\Models\PostJob;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class EmployerController extends Controller
{

    // public function index()
    // {
    //     try {


    //         $employers = EmployerDetail::with('user')->orderBy('id','desc')->get();
    //         $pageTitle = 'Employers List';

    //         return view('admin.employer.employer-list', compact('employers', 'pageTitle'));
    //     } catch (Exception $e) {
    //         return back()->with('error', 'Unable to load employer list.');
    //     }
    // }

    public function index(Request $request)
    {
        try {
            $query = EmployerDetail::query()
                ->with(['user', 'jobs'])
                ->select('employer_details.*');

            // Search Filter (Name, Email, Phone)
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            // Date Range Filter
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'DESC');

            if ($sortBy === 'name' || $sortBy === 'email') {
                // Sort by user relationship
                $query->join('users', 'employer_details.user_id', '=', 'users.id')
                    ->orderBy('users.' . $sortBy, $sortOrder)
                    ->select('employer_details.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination with query string persistence
            $employers = $query->paginate($request->get('per_page', 10))
                ->withQueryString();

            $pageTitle = 'Employers List';

            return view('admin.employer.employer-list', compact('employers', 'pageTitle'));
        } catch (Exception $e) {
            Log::error('Employers index error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load employer list.');
        }
    }


    public function jobApplications($job_id)
    {
        try {

            $job = Job::with([
                'appliedJobs.supervisor.user'
            ])->findOrFail($job_id);

            $pageTitle = 'Job Applications';

            return view('admin.employer.job-applications', compact('job', 'pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Unable to load job applications.');
        }
    }


    public function postedJobs($id)
    {

        $pageTitle = 'Employer Posted Jobs';

        $employer = EmployerDetail::with('user')->find($id);

        if (!$employer) {

            return redirect()->back()->with('error', 'Something went wrong please try again.');
        }

        $jobs = PostJob::with(['skills', 'experiences', 'appliedSupervisors'])
            ->where('employer_id', $id)
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('admin.employer.postedJobs', compact('pageTitle', 'jobs', 'employer'));
    }


    public function jobApplication($id)
    {
        try {


            $job = PostJob::where('id', $id)->with(['skills', 'experiences', 'appliedSupervisors.user'])
                ->firstOrFail();

             
            $pageTitle = 'Job Applications';

            return view('admin.employer.jobDetail', compact(
                'job',
                'pageTitle'
            ));
        } catch (Exception $e) {
            return back()->with('error', 'Unable to load employer job details.');
        }
    }
}
