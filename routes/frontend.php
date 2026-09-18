<?php

use App\Http\Controllers\Frontend\RegisterController;
use App\Http\Controllers\Supervisor\SupervisorRegisterController;
use App\Http\Controllers\Supervisor\SupervisorAuthController;
use App\Http\Controllers\Employer\EmployerRegisterController;
use App\Http\Controllers\Employer\EmployerLoginController;
use App\Http\Controllers\Frontend\Homecontroller;
use Illuminate\Support\Facades\Route;

Route::middleware(['roleRedirect'])->group(function () {
  
  Route::get('/register', [RegisterController::class, 'register'])->name('frontend.register');
  Route::get('/login', [RegisterController::class, 'login'])->name('frontend.login');

});

Route::get('/', [Homecontroller::class, 'index'])->name('frontend.home');
Route::get('/privacy-policy', [Homecontroller::class, 'privacy'])->name('frontend.privacy');
Route::get('/terms-&-conditions', [Homecontroller::class, 'termsConditions'])->name('frontend.termsConditions');
Route::post('/contact/submit', [Homecontroller::class, 'contactUs'])->name('contact.submit');