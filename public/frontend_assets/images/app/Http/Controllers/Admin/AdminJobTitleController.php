<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobTitle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminJobTitleController extends Controller
{
    // INDEX - LIST ALL JOB TITLES
    public function index(Request $request)
    {
        try {
            $query = JobTitle::query();

            // Search Filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('title', 'LIKE', "%{$search}%");
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'DESC');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $jobTitles = $query->paginate($request->get('per_page', 10))
                              ->withQueryString();

            $pageTitle = 'Job Titles';

            return view('admin.jobTitles.index', compact('jobTitles', 'pageTitle'));

        } catch (Exception $e) {
            Log::error('JobTitle index error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load job titles.');
        }
    }

    // CREATE - SHOW ADD FORM
    public function create()
    {
        try {
            $pageTitle = 'Add Job Title';
            return view('admin.jobTitles.create', compact('pageTitle'));
        } catch (Exception $e) {
            Log::error('JobTitle create view error: ' . $e->getMessage());
            return back()->with('error', 'Unable to open add job title page.');
        }
    }

    // STORE - SAVE NEW JOB TITLE
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:job_titles,title',
        ]

        );

        try {
            $jobTitle = new JobTitle();
            $jobTitle->title = $request->title;
            $jobTitle->created_by = 'admin'; 
            $jobTitle->user_id = Auth::id(); // Assuming user_id tracks the creator
            $jobTitle->save();

            return redirect()->route('admin.job-titles.index')
                ->with('success', 'Job Title added successfully');
        } catch (Exception $e) {
            Log::error('JobTitle store error: ' . $e->getMessage());
            return back()->with('error', 'Failed to add job title: ' . $e->getMessage())->withInput();
        }
    }

    // EDIT - SHOW EDIT FORM
    public function edit($id)
    {
        try {
            $jobTitle = JobTitle::findOrFail($id);
            $pageTitle = 'Edit Job Title';

            return view('admin.jobTitles.edit', compact('jobTitle', 'pageTitle'));
        } catch (Exception $e) {
            Log::error('JobTitle edit view error: ' . $e->getMessage());
            return back()->with('error', 'Job Title not found.');
        }
    }

    // UPDATE - UPDATE EXISTING JOB TITLE
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:job_titles,title,' . $id,
        ]);

        try {
            $jobTitle = JobTitle::findOrFail($id);
            $jobTitle->title = $request->title;
            // Not explicitly updating created_by or user_id on update as per typical CRUD patterns
            $jobTitle->save();

            return redirect()->route('admin.job-titles.index')
                ->with('success', 'Job Title updated successfully');
        } catch (Exception $e) {
            Log::error('JobTitle update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update job title: ' . $e->getMessage())->withInput();
        }
    }
}
