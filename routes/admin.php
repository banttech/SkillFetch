<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDepartmentController;
use App\Http\Controllers\Admin\SupervisorTestController;
use App\Http\Controllers\Admin\EmployerController;
use App\Http\Controllers\Admin\QualificationController;
use App\Http\Controllers\Admin\TestSettingController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminTestController;
use App\Http\Controllers\Admin\TestWindowController;
use App\Http\Controllers\Admin\AdminJobTitleController;

Route::prefix('admin')->group(function () {


    Route::middleware(['roleRedirect'])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'loginView'])->name('admin.login.view');
        Route::post('/login', [AdminAuthController::class, 'loginSubmit'])->name('admin.login.submit');

        Route::get('/forgot-password', [AdminAuthController::class, 'forgotPasswordView'])->name('admin.forgot.password.view');
        Route::post('/forgot-password', [AdminAuthController::class, 'forgotPasswordSubmit'])->name('admin.forgot.password.submit');

        Route::get('/reset-password/{token}', [AdminAuthController::class, 'resetPasswordView'])->name('admin.resetPassword.view');
        Route::post('/reset-password', [AdminAuthController::class, 'resetPasswordSubmit'])->name('admin.resetPassword.submit');
    });

    Route::middleware(['is_admin'])->group(function () {

        // LOGOUT

        Route::get('/dashboard', function () {
            return redirect()->route('admin.supervisor.supervisors-List');
        })->name('admin.dashboard');


        Route::get('/contacts', [AdminDashboardController::class, 'contacts'])->name('admin.contacts');

        Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        // SUPERVISORS
        Route::get('/supervisors-list', [SupervisorTestController::class, 'supervisorsList'])->name('admin.supervisor.supervisors-List');


        Route::get('/supervisors-qualification-test/{id}', [SupervisorTestController::class, 'supervisorsQualificationTest'])->name('admin.supervisor.supervisors-qualification-test');

        Route::get('/attempted-tests/{supervisorId}', [SupervisorTestController::class, 'index'])
            ->name('admin.test-review.index');

        Route::get('/test-review/{attemptId}', [SupervisorTestController::class, 'review'])
            ->name('admin.test-review.show');

        Route::post('/test-review/toggle-correction', [SupervisorTestController::class, 'toggleCorrection'])
            ->name('admin.test-review.toggle-correction');

        Route::post('/test-review/{attemptId}/verify', [SupervisorTestController::class, 'verify'])
            ->name('admin.test-review.verify');

        Route::post('/test-review/{attemptId}/reject', [SupervisorTestController::class, 'reject'])
            ->name('admin.test-review.reject');


        // EMPLOYERS
        Route::get('employer-list', [EmployerController::class, 'index'])->name('admin.employer.employerList');
        Route::get('/employer/posted-jobs/{id}', [EmployerController::class, 'postedJobs'])->name('admin.employerPostedJob');
        // Route::get('employer-job-details/{id}', [EmployerController::class, 'show'])->name('admin.employer.employer-job-details');

        // Route::get('employer-job-applications/{jobId}', [EmployerController::class, 'jobApplications'])->name('admin.employer.job.applications');

        Route::get('/job/applications/{id}', [EmployerController::class, 'jobApplication'])
            ->name('employer.jobApplication');


        // QUALIFICATION TEST
        Route::get('/qualification-test-setup', [QualificationController::class, 'index'])->name('admin.qualification.setuptest');

        Route::get('/qualification-test-add-question', [QualificationController::class, 'addQuestion'])->name('admin.qualification.add-question');
        Route::post('/qualification-test-store-question', [QualificationController::class, 'storeQuestion'])->name('admin.qualification.store-question');
        Route::get('/qualification-test-edit-question/{id}', [QualificationController::class, 'editQuestion'])->name('admin.qualification.edit-question');
        Route::post('/qualification-test-update-question/{id}', [QualificationController::class, 'updateQuestion'])->name('admin.qualification.update-question');
        Route::get('/qualification-test-delete-question/{id}', [QualificationController::class, 'deleteQuestion'])->name('admin.qualification.delete-question');

        Route::get('qualification/bulk-import',              [QualificationController::class, 'bulkImportView'])->name('admin.qualification.bulk-import');
        Route::post('qualification/bulk-import/process',      [QualificationController::class, 'processBulkImport'])->name('admin.qualification.bulk-import.process');
Route::get('/qualification/bulk-import/download', [QualificationController::class, 'bulkImportDownload'])
    ->name('admin.qualification.bulk-import.download');
        // TEST SETTINGS
        Route::get('/test-settings', [TestSettingController::class, 'index'])->name('admin.test-settings');
        Route::post('/test-settings/store', [TestSettingController::class, 'store'])->name('admin.test-settings.store');

        // PROFILE
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.view');
        Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');

        // PASSWORD
        Route::get('/password/edit', [AdminProfileController::class, 'editpassword'])->name('admin.password.edit');
        Route::post('/password/update', [AdminProfileController::class, 'updatepassword'])->name('admin.password.update');


        Route::prefix('departments')->name('admin.departments.')->group(function () {
            Route::get('/', [AdminDepartmentController::class, 'index'])->name('index');
            Route::get('/create', [AdminDepartmentController::class, 'create'])->name('create');
            Route::post('/store', [AdminDepartmentController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [AdminDepartmentController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [AdminDepartmentController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [AdminDepartmentController::class, 'destroy'])->name('destroy');
        });



        Route::prefix('job-titles')->name('admin.job-titles.')->group(function () {
            Route::get('/', [AdminJobTitleController::class, 'index'])->name('index');
            Route::get('/create', [AdminJobTitleController::class, 'create'])->name('create');
            Route::post('/store', [AdminJobTitleController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [AdminJobTitleController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [AdminJobTitleController::class, 'update'])->name('update');
        });



        // test routes 
        Route::prefix('tests')->name('admin.test.')->group(function () {
            Route::get('/', [AdminTestController::class, 'index'])->name('index');
            Route::get('/create', [AdminTestController::class, 'create'])->name('create');
            Route::post('/store', [AdminTestController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [AdminTestController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [AdminTestController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [AdminTestController::class, 'destroy'])->name('destroy');
        });


        Route::get('/settings/test-reattempt-period',    [TestWindowController::class, 'index'])->name('admin.settings.test-window.index');
        Route::put('/settings/test-window',    [TestWindowController::class, 'update'])->name('admin.settings.test-window.update');
    });
});
