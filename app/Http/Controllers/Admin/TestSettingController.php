<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TestSetting;
use App\Models\Department;

class TestSettingController extends Controller
{
    public function index()
    {
        $setting = TestSetting::first();
        $pageTitle = 'Test Settings';
        $departmentCount = Department::count();


        return view('admin.qualificationTest.testsetting', compact('setting', 'pageTitle', 'departmentCount'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'fees' => 'required|numeric',
            'name' => 'required|string|max:255',
            'marks' => 'required|numeric|min:1',
            'timing' => 'required|numeric',
            'question_per_department' => 'required|numeric|min:1',
            'no_of_departments' => 'required|numeric',
            'total_question' => 'required|numeric|min:1',
            'total_marks' => 'required|numeric',
            'passing_marks' => 'required|numeric|min:1|lte:total_marks',
        ],[
            'passing_marks.lte' => 'The passing marks must be less than the total marks',
            'passing_marks.min' => "The passing marks can't be less than 1"
        ]);

        $validated['no_of_departments'] = Department::count();

        $setting = TestSetting::first();

        if ($setting) {
            $setting->update($validated);
            return redirect()->back()->with('success', 'Test Settings Updated Successfully!');
        } else {
            TestSetting::create($validated);
            return redirect()->back()->with('success', 'Test Settings Saved Successfully!');
        }
    }
}
