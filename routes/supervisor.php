<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Supervisor\SupervisorRegisterController;
use App\Http\Controllers\Supervisor\SupervisorAuthController;
use App\Http\Controllers\Supervisor\SupervisorProfileController;
use App\Http\Controllers\Supervisor\PaymentController;
use App\Http\Controllers\Supervisor\TestController;
use App\Http\Controllers\Api\SupervisorPaymentWebhookController;
use App\Http\Controllers\Supervisor\AvailableTestsController;
use App\Http\Controllers\Supervisor\SupervisorDashboardController;
use App\Http\Controllers\Supervisor\SupervisorJobController;


// ------------------- Supervisor Registration -------------------
Route::prefix('supervisor')->group(function () {

    Route::middleware(['roleRedirect'])->group(function () {
        Route::get('/register', [SupervisorRegisterController::class, 'registerView'])
            ->name('supervisor.register.view');

        Route::post('/register/store', [SupervisorRegisterController::class, 'store'])
            ->name('supervisor.register.store');

        Route::post('/send-otp', [SupervisorRegisterController::class, 'sendOtp'])
            ->name('supervisor.send.otp');

        Route::post('/verify-otp', [SupervisorRegisterController::class, 'verifyOtp'])
            ->name('supervisor.verify.otp');


        // ------------------- Supervisor Login -------------------

        Route::get('/login', [SupervisorAuthController::class, 'loginView'])
            ->name('supervisor.login.view');

        Route::post('/login', [SupervisorAuthController::class, 'login'])
            ->name('supervisor.login');

        Route::post('/send-login-otp', [SupervisorAuthController::class, 'sendLoginOtp'])
            ->name('supervisor.send.login.otp');

        Route::post('/verify-login-otp', [SupervisorAuthController::class, 'verifyLoginOtp'])
            ->name('supervisor.verify.login.otp');



        Route::post('/payment/webhook', [SupervisorPaymentWebhookController::class, 'webhook']);

        Route::post('/check-email', [SupervisorRegisterController::class, 'checkEmail'])
            ->name('supervisor.check.email');
    });

    // =================== AUTH + SUPERVISOR MIDDLEWARE ===================

    Route::middleware(['is_supervisor'])->group(function () {

        // Dashboard
       

  Route::get('/tests/{testId}/attempt',  [AvailableTestsController::class, 'attempt'])->name('supervisor.tests.attempt');
        Route::post('/test/save-answer', [TestController::class, 'saveAnswer'])
            ->name('supervisor.test.save-answer');

        Route::post('/test/submit', [TestController::class, 'submitTest'])
            ->name('supervisor.test.submit');

        Route::get('/test/remaining-time', [TestController::class, 'getRemainingTime'])
            ->name('supervisor.test.remaining-time');

             Route::post('/test/log-violation', [TestController::class, 'logViolation'])
            ->name('supervisor.test.log-violation');

        Route::post('/test/log-tab-switch', [TestController::class, 'logTabSwitch'])
            ->name('supervisor.test.log-tab-switch');

        Route::post('/test/log-camera-activity', [TestController::class, 'logCameraActivity'])
            ->name('supervisor.test.log-camera-activity');



        Route::get('/logout', [SupervisorAuthController::class, 'logout'])
            ->name('supervisor.logout');


 Route::middleware(['IsTestRunning'])->group(function () {

  Route::get('/dashboard', [SupervisorDashboardController::class, 'dashboard'])
            ->name('supervisor.dashboard');
        // Profile
        Route::get('/profile/edit', [SupervisorProfileController::class, 'edit'])
            ->name('supervisor.profile.edit');
        Route::post('/profile/update', [SupervisorProfileController::class, 'update'])
            ->name('supervisor.profile.update');

        // Payment
        Route::post('/payment/initiate', [PaymentController::class, 'initiate'])
            ->name('supervisor.payment.initiate');
        Route::post('/supervisor/payment/verify',   [PaymentController::class, 'verify'])->name('supervisor.payment.verify');


        // =================== TEST ROUTES ===================


       
        // Open Jobs
        Route::get('/open-jobs', [SupervisorJobController::class, 'openJobs'])->name('supervisor.openjobs');

        // Job Detail (works for both open and applied jobs)
        Route::get('/job-detail/{id}', [SupervisorJobController::class, 'jobDetail'])->name('supervisor.jobDetail');

        // Apply for Job
        Route::post('/jobs/{id}/apply', [SupervisorJobController::class, 'applyJob'])->name('supervisor.apply.job');

        // Applied Jobs
        Route::get('/applied-jobs', [SupervisorJobController::class, 'appliedJobs'])->name('supervisor.appliedJobs');


        Route::get('/tests',                  [AvailableTestsController::class, 'index'])->name('supervisor.tests.index');
        Route::get('/tests/{testId}',          [AvailableTestsController::class, 'show'])->name('supervisor.tests.show');
      });
    });
});
