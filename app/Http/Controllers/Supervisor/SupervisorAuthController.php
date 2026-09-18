<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Employer\OtpTrait;

class SupervisorAuthController extends Controller
{
    use OtpTrait;

    public function loginView()
    {
        $pageTitle = 'Supervisor Login';
        return view('supervisor.auth.login', compact('pageTitle'));
    }

    public function sendLoginOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|digits:10|regex:/^[6-9]\d{9}$/'
            ], [
                'phone.required' => 'Mobile number is required',
                'phone.digits' => 'Mobile number must be exactly 10 digits',
                'phone.regex' => 'Please enter a valid mobile number'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // CHECK IF SUPERVISOR EXISTS
            $user = User::where('phone', $request->phone)
                ->where('role_id', 3)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'not_registered' => true,
                    'message' => 'Supervisor record not found. Please complete registration first.'
                ], 422);
            }

            // Check if user is active
            if ($user->status != 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is inactive. Please contact support.'
                ], 422);
            }

            // Check rate limiting - prevent spam (max 3 OTPs in 5 minutes)
            $recentOtps = OtpValidation::where('phone', $request->phone)
                ->where('type', 'supervisor_login')
                ->where('created_at', '>', now()->subMinutes(5)) // FIXED: Was 1 minute
                ->count();

            if ($recentOtps >= 3) {
                return response()->json([
                    'status' => false,
                    'message' => 'Too many OTP requests. Please try again after 5 minutes.'
                ], 429);
            }

            // GENERATE NEW OTP (createOtp will delete old ones)
            $otp = $this->createOtp($request->phone, 'supervisor_login');

            if (isset($otp['error'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to generate OTP. Please try again.'
                ], 500);
            }

            // Store phone in session temporarily
            session(['temp_login_phone' => $request->phone]);

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully to your mobile number',
                'otp' => $otp // Remove in production
            ]);
        } catch (\Exception $e) {
            Log::error('Login OTP Send Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    public function verifyLoginOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
                'otp' => 'required|digits:6'
            ], [
                'otp.required' => 'Please enter the OTP',
                'otp.digits' => 'OTP must be 6 digits',
                'phone.regex' => 'Please enter a valid mobile number'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $result = $this->validateOtp($request->phone, $request->otp, 'supervisor_login');

            if (isset($result['error'])) {
                return response()->json([
                    'status' => false,
                    'message' => $result['error']
                ], 422);
            }

            // Lock the verified phone number in session
            session([
                'verified_login_phone' => $request->phone,
                'login_phone_locked_at' => now()->timestamp
            ]);

            // Remove temp phone
            session()->forget('temp_login_phone');

            return response()->json([
                'status' => true,
                'message' => 'OTP Verified Successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Login OTP Verify Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'OTP verification failed. Please try again.'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate(['phone' => 'required|digits:10']);

            // CRITICAL: Check session-locked phone number
            $verifiedPhone = session('verified_login_phone');
            $lockedAt = session('login_phone_locked_at');

            // Check if phone was verified in last 5 minutes
            if (!$verifiedPhone || !$lockedAt || (now()->timestamp - $lockedAt) > 300) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP verification expired or not completed. Please verify again.'
                ], 422);
            }

            // CRITICAL: Submitted phone MUST match session-locked phone
            if ($request->phone !== $verifiedPhone) {
                return response()->json([
                    'status' => false,
                    'message' => 'Security validation failed. Phone number does not match verified number.'
                ], 422);
            }

            // Double-check OTP verification in database
            $otpVerified = OtpValidation::where('phone', $request->phone)
                ->where('type', 'supervisor_login')
                ->where('is_verified', 1)
                ->first();

            if (!$otpVerified) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP not verified. Please verify your mobile number again.'
                ], 422);
            }

            $user = User::where('phone', $request->phone)
                ->where('role_id', 3)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Supervisor not found'
                ], 403);
            }

            if ($user->status != 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is inactive. Please contact support.'
                ], 403);
            }

            Auth::login($user);

            // Remove used OTP after login
            OtpValidation::where('phone', $request->phone)
                ->where('type', 'supervisor_login')
                ->delete();

            // Clear session data
            session()->forget(['verified_login_phone', 'login_phone_locked_at']);

            return response()->json([
                'status' => true,
                'message' => 'Login Successful',
                'redirect' => route('supervisor.dashboard')
            ]);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Login failed. Please try again.'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('supervisor.login.view')
            ->with('success', 'Logged out successfully');
    }
}
