<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class TestWindowController extends Controller
{
    public function index()
    {
        $pageTitle = 'Test Reattempt Period';
        $hours     = AppSetting::testAttemptWindowHours();

        return view('admin.settings.test-window', compact('pageTitle', 'hours'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hours' => 'required|integer|min:1|max:168', // max 1 week
        ], [
            'hours.required' => 'Please enter the Reattempt Period in hours.',
            'hours.min'      => 'Minimum Reattempt Period is 1 hour.',
            'hours.max'      => 'Maximum Reattempt Period is 168 hours (7 days).',
        ]);

        AppSetting::set('test_attempt_window_hours', $request->hours);

        return redirect()->back()->with('success', 'Test attempt window updated to ' . $request->hours . ' hour(s) successfully.');
    }
}