<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployerDetail;
use App\Models\OtpValidation;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmployerRegisterController extends Controller
{
    use OtpTrait;

    public function registerView()
    {
        $pageTitle = "Employer Register";
        $states = State::orderby('name', 'asc')->get();
        return view('employer.auth.register', compact('pageTitle', 'states'));
    }

    public function sendOtp(Request $request)
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

            // Check if phone already registered
            if (User::where('phone', $request->phone)->exists()) {
                return response()->json([
                    'status' => false,
                    'already_registered' => true,
                    'message' => 'This mobile number is already registered. Please login.'
                ], 422);
            }

            // Check rate limiting - prevent spam (max 3 OTPs in 5 minutes)
            $recentOtps = OtpValidation::where('phone', $request->phone)
                ->where('type', 'employer_register')
                ->where('created_at', '>', now()->subMinutes(5))
                ->count();

            if ($recentOtps >= 3) {
                return response()->json([
                    'status' => false,
                    'message' => 'Too many OTP requests. Please try again after 5 minutes.'
                ], 429);
            }

            // GENERATE NEW OTP (createOtp will delete old ones automatically)
            $otp = $this->createOtp($request->phone, 'employer_register');

            if (isset($otp['error'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to generate OTP. Please try again.'
                ], 500);
            }

            // Store phone in session temporarily (will be locked after verification)
            session(['temp_employer_registration_phone' => $request->phone]);

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully to your mobile number',
                'otp' => $otp // Remove in production
            ]);

        } catch (\Exception $e) {
            Log::error('OTP Send Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
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

            $result = $this->validateOtp($request->phone, $request->otp, 'employer_register');

            if (isset($result['error'])) {
                return response()->json([
                    'status' => false,
                    'message' => $result['error']
                ], 422);
            }

            // Lock the verified phone number in session
            session([
                'verified_employer_registration_phone' => $request->phone,
                'employer_registration_phone_locked_at' => now()->timestamp
            ]);

            // Remove temp phone
            session()->forget('temp_employer_registration_phone');

            return response()->json([
                'status' => true,
                'message' => 'Mobile number verified successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('OTP Verify Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'OTP verification failed. Please try again.'
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            // CRITICAL: Check session-locked phone number
            $verifiedPhone = session('verified_employer_registration_phone');
            $lockedAt = session('employer_registration_phone_locked_at');

            // Check if phone was verified in last 10 minutes (matching supervisor)
            if (!$verifiedPhone || !$lockedAt || (now()->timestamp - $lockedAt) > 600) {
                return response()->json([
                    'status' => false,
                    'errors' => ['phone' => ['OTP verification expired or not completed.']],
                    'message' => 'OTP verification expired or not completed.'
                ], 422);
            }

            // CRITICAL: Submitted phone MUST match session-locked phone
            if ($request->phone !== $verifiedPhone) {
                return response()->json([
                    'status' => false,
                    'errors' => ['phone' => ['Phone number does not match verified number.']],
                    'message' => 'Security validation failed. Please verify your phone again.'
                ], 422);
            }

            // Double-check OTP verification in database
            $otpRecord = OtpValidation::where('phone', $request->phone)
                ->where('type', 'employer_register')
                ->where('is_verified', 1)
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'status' => false,
                    'errors' => ['phone' => ['OTP verification not found.']],
                    'message' => 'Please verify your mobile number again.'
                ], 422);
            }

            // Validate all fields
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|regex:/^[A-Za-z ]+$/',
                'email' => 'required|email|unique:users,email',
                'phone' => ['required', 'unique:users,phone', 'regex:/^[6-9]\d{9}$/'],
                'otp' => ['required', 'digits:6'],
                'company_name' => 'required|string|max:255|regex:/^[A-Za-z ]+$/',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:100|regex:/^[A-Za-z ]+$/',
                'state' => 'required|string|max:100',
                'pin_code' => ['required', 'digits:6'],
                'identity' => 'required|file|mimes:pdf,doc,docx|max:3072',
            ], [
                'name.required' => 'Name is required',
                'name.regex' => 'Name must contain alphabets only',
                'email.required' => 'Email is required',
                'email.unique' => 'This email is already registered',
                'phone.required' => 'Mobile number is required',
                'phone.unique' => 'This mobile number is already registered',
                'phone.digits' => 'Mobile number must be 10 digits',
                'otp.required' => 'OTP is required',
                'otp.digits' => 'OTP must be 6 digits',
                'company_name.required' => 'Company name is required',
                'company_name.regex' => 'Company name must contain alphabets only',
                'city.regex' => 'City must contain alphabets only',
                'pin_code.digits' => 'Pin code must be 6 digits',
                'identity.required' => 'Identity proof is required',
                'identity.mimes' => 'Identity proof must be PDF or DOC file',
                'identity.max' => 'Identity proof size should not exceed 3MB',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Please fix the errors.'
                ], 422);
            }

            // Start transaction
            DB::beginTransaction();

            // CREATE USER
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'role_id' => 2,
                'status' => 1,
                'verfied' => 1,
            ]);

            // UPLOAD IDENTITY
            $identityPath = $request->file('identity')->store('identity_proofs', 'public');

            // CREATE EMPLOYER DETAIL
            EmployerDetail::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'pin_code' => $request->pin_code,
                'identity_proof_path' => $identityPath,
            ]);

            // Delete used OTP
            OtpValidation::where('phone', $request->phone)
                ->where('type', 'employer_register')
                ->delete();

            // Clear session data
            session()->forget(['verified_employer_registration_phone', 'employer_registration_phone_locked_at']);

            DB::commit();

            session()->flash('success', 'Registration completed successfully. You may now log in.');

            return response()->json([
                'status' => true,
                'message' => 'Registration completed successfully. You may now log in.',
                'redirect' => route('employer.login.view')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employer Registration Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }
}