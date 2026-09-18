<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployerSkill;
use Illuminate\Support\Facades\Auth;

class EmployerSkillController extends Controller
{
    public function index()
    {
         $pageTitle = 'Employer Skills';
        $employerId = Auth::user()->employerDetail->id;
        $skills = EmployerSkill::where('employer_id', $employerId)->orderBy('id', 'desc')->get();
        return view('employer.employerSkill.index', compact('skills', 'pageTitle'));
    }

    public function create()
    {

       $pageTitle = 'Post New Skill';

        return view('employer.employerSkill.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'skills' => 'required|string|unique:employer_skills,skills',
        ]);

        try {
            $employerId = Auth::user()->employerDetail->id;

            EmployerSkill::create([
                'employer_id' => $employerId,
                'skills' => $request->skills,
            ]);

            return redirect()->route('employer.skill.index')
                ->with('success', 'Skill Added Successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add skill: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $employerId = Auth::user()->employerDetail->id;

            $skill = EmployerSkill::where('id', $id)
                ->where('employer_id', $employerId)
                ->firstOrFail();

            $pageTitle = 'Edit Skill';
            return view('employer.employerSkill.edit', compact('skill', 'pageTitle'));
        } catch (\Exception $e) {
            return redirect()->route('employer.skill.index')
                ->with('error', 'Unable to load skill: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'skills' => 'required|string|unique:employer_skills,skills,' . $id,
        ]);

        try {
            $employerId = Auth::user()->employerDetail->id;

            $skill = EmployerSkill::where('id', $id)
                ->where('employer_id', $employerId)
                ->firstOrFail();

            $skill->update([
                'skills' => $request->skills,
            ]);

            return redirect()->route('employer.skill.index')
                ->with('success', 'Skill Updated Successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update skill: ' . $e->getMessage());
        }
    }
}
