<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use App\Models\Skill;
use App\Models\WorkLocation;
use Illuminate\Http\Request;

class EmployerSupervisorController extends Controller
{
    public function index(Request $request)
    {
        try {

            // Fetch filters from DB
            $skills = Skill::orderBy('name')->get();
            $locations = WorkLocation::orderBy('name')->get();

            // Base Query
            $query = Supervisor::with(['user', 'skills', 'workLocations']);

            // Name filter
            if ($request->filled('name')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->name}%");
                });
            }

            // City filter (simple direct text input)
            if ($request->filled('city')) {
                $query->where('city', 'like', "%{$request->city}%");
            }

            // Preferred location filter
            if ($request->filled('preferred_location')) {
                $query->whereHas('workLocations', function ($q) use ($request) {
                    $q->where('name', $request->preferred_location);
                });
            }

            // Own bike filter
            if ($request->filled('own_bike')) {
                $query->where('own_bike', $request->own_bike);
            }

            // Skills filter
            if ($request->filled('skill_tag')) {
                $query->whereHas('skills', function ($q) use ($request) {
                    $q->where('name', $request->skill_tag);
                });
            }

            $supervisors = $query->get();

            return view('employer.supervisor.search-supervisor', [
                'page_title' => 'Search Supervisors',
                'supervisors' => $supervisors,

                'skills' => $skills,
                'locations' => $locations,
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Error fetching supervisors: ' . $e->getMessage());
        }
    }

    public function profile($id)
    {
        try {

            $sup = Supervisor::with(['user', 'skills', 'experiences', 'workLocations'])
                ->findOrFail($id);

            return view('employer.supervisor.profile', [
                'sup' => $sup,
                'page_title' => 'Supervisor Profile',
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Unable to load supervisor profile: ' . $e->getMessage());
        }
    }
}
