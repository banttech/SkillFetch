<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
// Web Routes
require base_path('routes/employer.php');
require base_path('routes/admin.php');
require base_path('routes/supervisor.php');
require base_path('routes/frontend.php');

// ── Shared authenticated routes (accessible from any panel) ──────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/test-score/department/{attemptId}',
        [\App\Http\Controllers\Admin\SupervisorTestController::class, 'departmentScore'])
        ->name('test.dept-score');
});









