<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PostJob;

class MyjobController extends Controller
{
    public function index()
    {
        try {
            // Get logged-in employer_details ID
            $employerId = Auth::user()->employerDetail->id;

          
            // Load Jobs + Payment + Skills + Experience
            $jobs = PostJob::with(['payment', 'skills.skill', 'experiences.experience'])
                ->where('employer_id', $employerId) 
                ->orderBy('id', 'DESC')
                ->paginate(10);

            return view('employer.myjobs', [
                'jobs' => $jobs,
                'pageTitle' => 'My Jobs'
            ]);

        } catch (\Exception $e) {
            // dd($e->getMessage());
            return back()->with('error', 'Unable to load jobs: ' . $e->getMessage());
        }
    }
}
