<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminTestController extends Controller
{
    /**
     * Display a listing of the tests.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Tests';
        
        $query = Test::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('fees', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'DESC');
        
        $allowedSortFields = ['name', 'fees', 'timing', 'total_marks', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        // Pagination
        $perPage = $request->get('per_page', 10);
        $tests = $query->paginate($perPage)->withQueryString();
        
        return view('admin.tests.index', compact('tests', 'pageTitle'));
    }

    /**
     * Show the form for creating a new test.
     */
    public function create()
    {
        $pageTitle = 'Add Test';
        $departmentCount = Department::count();

        return view('admin.tests.create', compact('pageTitle', 'departmentCount'));
    }

    /**
     * Store a newly created test in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255|unique:tests,name',
            'fees' => 'required|numeric|min:0',
            'timing' => 'required|integer|in:60,75,90',
            
            'text_marks' => 'required|numeric|min:0',
            'text_q_per_dept' => 'required|integer|min:0',
            
            'single_select_marks' => 'required|numeric|min:0',
            'single_select_q_per_dept' => 'required|integer|min:0',
            
            'multi_select_marks' => 'required|numeric|min:0',
            'multi_select_q_per_dept' => 'required|integer|min:0',
            
            'image_marks' => 'required|numeric|min:0',
            'image_q_per_dept' => 'required|integer|min:0',
            
            'video_marks' => 'required|numeric|min:0',
            'video_q_per_dept' => 'required|integer|min:0',
            
            'no_of_departments' => 'required|integer|min:1',
            'question_per_department' => 'required|integer|min:1',
            'total_question' => 'required|integer|min:1',
            'total_marks' => 'required|numeric|min:1',
            'passing_marks' => 'required|numeric|min:0|lt:total_marks',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Test name is required',
            'name.unique' => 'Test name already exists',
            'fees.required' => 'Test fee is required',
            'fees.min' => 'Test fee cannot be negative',
            'timing.required' => 'Test timing is required',
            'timing.in' => 'Test timing must be 60, 75, or 90 minutes',
            'passing_marks.lt' => 'Passing marks must be less than total marks',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be either active or inactive',
        ]);

        try {
            Test::create($validated);
            
            return redirect()->route('admin.test.index')
                ->with('success', 'Test created successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create test. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified test.
     */
    public function edit($id)
    {
        $pageTitle = 'Edit Test';
        $test = Test::findOrFail($id);
        $departmentCount = Department::count();

        return view('admin.tests.edit', compact('test', 'pageTitle', 'departmentCount'));
    }

    /**
     * Update the specified test in storage.
     */
    public function update(Request $request, $id)
    {
        $test = Test::findOrFail($id);
        
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('tests', 'name')->ignore($test->id)
            ],
            'fees' => 'required|numeric|min:0',
            'timing' => 'required|integer|in:60,75,90',
            
            'text_marks' => 'required|numeric|min:0',
            'text_q_per_dept' => 'required|integer|min:0',
            
            'single_select_marks' => 'required|numeric|min:0',
            'single_select_q_per_dept' => 'required|integer|min:0',
            
            'multi_select_marks' => 'required|numeric|min:0',
            'multi_select_q_per_dept' => 'required|integer|min:0',
            
            'image_marks' => 'required|numeric|min:0',
            'image_q_per_dept' => 'required|integer|min:0',
            
            'video_marks' => 'required|numeric|min:0',
            'video_q_per_dept' => 'required|integer|min:0',
            
            'no_of_departments' => 'required|integer|min:1',
            'question_per_department' => 'required|integer|min:1',
            'total_question' => 'required|integer|min:1',
            'total_marks' => 'required|numeric|min:1',
            'passing_marks' => 'required|numeric|min:0|lt:total_marks',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Test name is required',
            'name.unique' => 'Test name already exists',
            'fees.required' => 'Test fee is required',
            'fees.min' => 'Test fee cannot be negative',
            'timing.required' => 'Test timing is required',
            'timing.in' => 'Test timing must be 60, 75, or 90 minutes',
            'passing_marks.lt' => 'Passing marks must be less than total marks',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be either active or inactive',
        ]);

        try {
            $test->update($validated);
            
            return redirect()->route('admin.test.index')
                ->with('success', 'Test updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update test. Please try again.');
        }
    }

    /**
     * Remove the specified test from storage.
     */
    public function destroy($id)
    {
        try {
            $test = Test::findOrFail($id);
            $test->delete();
            
            return redirect()->route('admin.test.index')
                ->with('success', 'Test deleted successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete test. Please try again.');
        }
    }
}