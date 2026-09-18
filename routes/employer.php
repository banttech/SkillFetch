<?php

use App\Http\Controllers\Employer\EmployerRegisterController;
use App\Http\Controllers\Employer\EmployerLoginController;
use App\Http\Controllers\Employer\EmployerProfileController;
use App\Http\Controllers\Employer\EmployerJobPaymentController;
use App\Http\Controllers\Api\EmployerPaymentWebhookController;
use App\Http\Controllers\Employer\EmployerJobPostController;
use App\Http\Controllers\Employer\EmployerSearchSupervisorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::prefix('employer')->group(function () {


    Route::middleware(['roleRedirect'])->group(function () {
        // EMPLOYER REGISTER
        Route::get('/register', [EmployerRegisterController::class, 'registerView'])
            ->name('employer.register.view');

        Route::post('/send-otp', [EmployerRegisterController::class, 'sendOtp'])
            ->name('employer.send-otp');

        Route::post('/verify-otp', [EmployerRegisterController::class, 'verifyOtp'])
            ->name('employer.verify-otp');

        Route::post('/register', [EmployerRegisterController::class, 'register'])
            ->name('employer.register');


        // EMPLOYER LOGIN
        Route::get('/login', [EmployerLoginController::class, 'loginView'])
            ->name('employer.login.view');

        Route::post('/send-login-otp', [EmployerLoginController::class, 'sendLoginOtp'])
            ->name('employer.send-login-otp');

        Route::post('/verify-login-otp', [EmployerLoginController::class, 'verifyLoginOtp'])
            ->name('employer.verify-login-otp');

        Route::post('/login', [EmployerLoginController::class, 'login'])
            ->name('employer.login.submit');
        Route::post('/check-email', [EmployerRegisterController::class, 'checkEmail'])
            ->name('employer.check.email');
    });

    // EMPLOYER AUTH ROUTES
    Route::middleware(['is_employer'])->group(function () {


        Route::get('/dashboard', function () {
            return redirect()->route('employer.myjobs');
        })->name('employer.dashboard');

        Route::get('/myjobs', [EmployerJobPostController::class, 'index'])
            ->name('employer.myjobs');
        Route::post('/job/fees', [EmployerJobPaymentController::class, 'createOrder'])
            ->name('employer.job.fees');


        // SKILL MODULE
        Route::get('/skill', [\App\Http\Controllers\Employer\EmployerSkillController::class, 'index'])
            ->name('employer.skill.index');
        Route::get('/skill/create', [\App\Http\Controllers\Employer\EmployerSkillController::class, 'create'])
            ->name('employer.skill.create');
        Route::post('/skill/store', [\App\Http\Controllers\Employer\EmployerSkillController::class, 'store'])
            ->name('employer.skill.store');
        Route::get('/skill/edit/{id}', [\App\Http\Controllers\Employer\EmployerSkillController::class, 'edit'])
            ->name('employer.skill.edit');
        Route::put('/skill/update/{id}', [\App\Http\Controllers\Employer\EmployerSkillController::class, 'update'])
            ->name('employer.skill.update');

        // JOB MODULE
        Route::post('/job/title/create', [EmployerJobPostController::class, 'storeJobTitle'])
            ->name('employer.job.title.store');
        Route::get('/job/titles', [EmployerJobPostController::class, 'getJobTitles'])
            ->name('employer.job.titles.list');

        Route::get('/job/create', [EmployerJobPostController::class, 'create'])
            ->name('employer.job.create');

        Route::post('/job/store', [EmployerJobPostController::class, 'store'])
            ->name('employer.job.store');

        Route::get('/job/edit/{id}', [EmployerJobPostController::class, 'edit'])
            ->name('employer.job.edit');

        Route::put('/job/update/{id}', [EmployerJobPostController::class, 'update'])
            ->name('employer.job.update');

        Route::get('/job/detail/{id}', [EmployerJobPostController::class, 'details'])
            ->name('employer.job.details');
            
        Route::get('/job/detail/{id}/export', [EmployerJobPostController::class, 'exportAppliedSupervisors'])
            ->name('employer.job.export-supervisors');

        Route::post('/job-payment/initiate', [EmployerJobPostController::class, 'paymentInitiate'])
            ->name('employer.job-payment.initiate');


        // SEARCH SUPERVISOR

        // Supervisor Search
        Route::get('/search-supervisor', [EmployerSearchSupervisorController::class, 'index'])
            ->name('employer.search.supervisor');

        Route::get('/search-supervisor/export', [EmployerSearchSupervisorController::class, 'exportUnlockedSupervisors'])
            ->name('employer.search.supervisor.export');

        Route::get('/supervisor/profile/{id}', [EmployerSearchSupervisorController::class, 'profile'])
            ->name('employer.supervisor.profile');

        Route::post('/supervisor/profile-view-payment', [EmployerSearchSupervisorController::class, 'initiateProfileViewPayment'])
            ->name('employer.supervisor.profile-view-payment');


        // MANAGE PROFILE DROPDOWN ROUTES
        // PROFILE VIEW
        Route::get('/profile', [EmployerProfileController::class, 'profile'])
            ->name('employer.profile');

        // UPDATE PROFILE
        Route::post('/profile/update', [EmployerProfileController::class, 'updateProfile'])
            ->name('employer.profile.update');



        Route::post('/payment/webhook', [EmployerPaymentWebhookController::class, 'webhook']);

        // LOGOUT
        Route::get('/logout', function () {
            Auth::logout();
            return redirect()->route('employer.login.view')
                ->with('message', 'Logged out successfully');
        })->name('employer.logout');
    });
});
