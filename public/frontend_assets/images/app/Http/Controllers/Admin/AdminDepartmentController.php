<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminDepartmentController extends Controller
{
    // INDEX - LIST ALL DEPARTMENTS
    public function index(Request $request)
    {
        try {
            $query = Department::query()
                ->select('departments.*');

            // Search Filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('name', 'LIKE', "%{$search}%");
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'DESC');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $departments = $query->paginate($request->get('per_page', 10))
                              ->withQueryString();

            $pageTitle = 'Departments';

            return view('admin.departments.index', compact('departments', 'pageTitle'));

        } catch (Exception $e) {
            Log::error('Department index error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load departments.');
        }
    }

    // CREATE - SHOW ADD FORM
    public function create()
    {
        try {
            $pageTitle = 'Add Department';
            return view('admin.departments.create', compact('pageTitle'));
        } catch (Exception $e) {
            Log::error('Department create view error: ' . $e->getMessage());
            return back()->with('error', 'Unable to open add department page.');
        }
    }

    // STORE - SAVE NEW DEPARTMENT
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
           
        ]);

        try {
            $department = new Department();
            $department->name = $request->name;
            $department->save();

            return redirect()->route('admin.departments.index')
                ->with('success', 'Department added successfully');
        } catch (Exception $e) {
            Log::error('Department store error: ' . $e->getMessage());
            return back()->with('error', 'Failed to add department: ' . $e->getMessage())->withInput();
        }
    }

    // EDIT - SHOW EDIT FORM
    public function edit($id)
    {
        try {
            $department = Department::findOrFail($id);
            $pageTitle = 'Edit Department';

            return view('admin.departments.edit', compact('department', 'pageTitle'));
        } catch (Exception $e) {
            Log::error('Department edit view error: ' . $e->getMessage());
            return back()->with('error', 'Department not found.');
        }
    }

    // UPDATE - UPDATE EXISTING DEPARTMENT
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
           
        ]);

        try {
            $department = Department::findOrFail($id);
            $department->name = $request->name;
            $department->save();

            return redirect()->route('admin.departments.index')
                ->with('success', 'Department updated successfully');
        } catch (Exception $e) {
            Log::error('Department update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update department: ' . $e->getMessage())->withInput();
        }
    }

    // DELETE - DELETE DEPARTMENT
    public function destroy($id)
    {
        try {
            $department = Department::findOrFail($id);
            $department->delete();

            return redirect()->route('admin.departments.index')
                ->with('success', 'Department deleted successfully');
        } catch (Exception $e) {
            Log::error('Department delete error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete department. It may be in use.');
        }
    }
}