<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\TestCameraActivity;
use Illuminate\Http\Request;

class CameraActivityController extends Controller
{
   public function store(Request $request)
    {
        TestCameraActivity::create([
            'test_id' => $request->test_id,
            'user_id' => auth()->id(),
            'activity_type' => $request->activity_type,
            'message' => $request->message,
        ]);

        return response()->json(['success' => true]);
    }
}
