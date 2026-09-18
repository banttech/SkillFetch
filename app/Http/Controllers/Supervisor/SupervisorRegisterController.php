<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Supervisor;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\WorkLocation;
use App\Models\State;
use App\Models\OtpValidation;
use App\Http\Controllers\Employer\OtpTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SupervisorRegisterController extends Controller
{
    use OtpTrait;

    public function registerView()
    {
        return view('supervisor.auth.register', [
            'pageTitle' => 'Supervisor Registration',
            'states' => State::orderby('name', 'asc')->get(),
            'skills' => Skill::orderby('name', 'asc')->get(),
            'locations' => WorkLocation::orderby('name', 'asc')->get(),
            'experiences' => Experience::orderby('name', 'asc')->get(),
        ]);
    }

   public function sendOtp(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/'
        ], [
            'mobile.required' => 'Mobile number is required',
            'mobile.digits' => 'Mobile number must be exactly 10 digits',
            'mobile.regex' => 'Please enter a valid mobile number'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Check if mobile already registered
        if (User::where('phone', $request->mobile)->exists()) {
            return response()->json([
                'success' => false,
                'already_registered' => true,
                'message' => 'This mobile number is already registered. Please login.'
            ], 422);
        }

        // Check rate limiting - prevent spam (max 3 OTPs in 5 minutes)
        $recentOtps = OtpValidation::where('phone', $request->mobile)
            ->where('type', 'supervisor_register')
            ->where('created_at', '>', now()->subMinutes(5))
            ->count();

        if ($recentOtps >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP requests. Please try again after 5 minutes.'
            ], 429);
        }

        // GENERATE NEW OTP (createOtp will delete old ones automatically)
        $otp = $this->createOtp($request->mobile, 'supervisor_register');

        if (isset($otp['error'])) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate OTP. Please try again.'
            ], 500);
        }

        // Store phone in session temporarily (will be locked after verification)
        session(['temp_registration_phone' => $request->mobile]);

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully to your mobile number',
            'otp' => $otp // Remove in production
        ]);

    } catch (\Exception $e) {
        Log::error('OTP Send Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP. Please try again.'
        ], 500);
    }
}

    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
                'otp' => 'required|digits:6'
            ], [
                'otp.required' => 'Please enter the OTP',
                'otp.digits' => 'OTP must be 6 digits',
                'mobile.regex' => 'Please enter a valid mobile number'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $result = $this->validateOtp($request->mobile, $request->otp, 'supervisor_register');

            if (isset($result['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 422);
            }

            // Lock the verified phone number in session
            session([
                'verified_registration_phone' => $request->mobile,
                'registration_phone_locked_at' => now()->timestamp
            ]);

            // Remove temp phone
            session()->forget('temp_registration_phone');

            return response()->json([
                'status' => true,
                'message' => 'Mobile number verified successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('OTP Verify Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed. Please try again.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // CRITICAL: Check session-locked phone number
           $verifiedPhone = session('verified_registration_phone');
        $lockedAt = session('registration_phone_locked_at');

        // Check if phone was verified in last 10 minutes (FIXED: Was 60 seconds)
        if (!$verifiedPhone || !$lockedAt || (now()->timestamp - $lockedAt) > 600) {
            return response()->json([
                'status' => false,
                'errors' => ['mobile' => ['OTP verification expired or not completed.']],
                'message' => 'OTP verification expired or not completed.'
            ], 422);
        }

            // CRITICAL: Submitted phone MUST match session-locked phone
            if ($request->mobile !== $verifiedPhone) {
                return response()->json([
                    'status' => false,
                    'errors' => ['mobile' => ['Phone number does not match verified number.']],
                    'message' => 'Security validation failed. Please verify your phone again.'
                ], 422);
            }

            // Double-check OTP verification in database
            $otpRecord = OtpValidation::where('phone', $request->mobile)
                ->where('type', 'supervisor_register')
                ->where('is_verified', 1)
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'status' => false,
                    'errors' => ['mobile' => ['OTP verification not found.']],
                    'message' => 'Please verify your mobile number again.'
                ], 422);
            }

            // Validate all fields
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'mobile' => ['required', 'unique:users,phone', 'regex:/^[6-9]\d{9}$/'],
                'otp' => ['required', 'digits:6'],
                'salary'=> 'required|numeric',
                'address' => 'required|string|max:255',
                'city' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s]+$/'],
                'state_id' => 'required|integer|exists:states,id',
                'pincode' => ['required', 'digits:6'],
                'own_bike' => 'required|in:Yes,No',
                'own_phone' => 'required|in:Yes,No',
                'skills' => 'required|array|min:1',
                'skills.*' => 'required|integer|exists:skills,id',
                'experiences' => 'required|array|min:1',
                'experiences.*' => 'required|integer|exists:experiences,id',
                'locations' => 'required|array|min:1',
                'locations.*' => 'required|integer|exists:work_locations,id',
                'aadhar' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg,webp|max:2048',
            ], [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.unique' => 'This email is already registered',
                'mobile.required' => 'Mobile number is required',
                'mobile.unique' => 'This mobile number is already registered',
                'mobile.digits' => 'Mobile number must be 10 digits',
                'otp.required' => 'OTP is required',
                'otp.digits' => 'OTP must be 6 digits',
                'city.regex' => 'City should contain only alphabets',
                'pincode.digits' => 'Pincode must be 6 digits',
                'skills.required' => 'Please select at least one skill',
                'experiences.required' => 'Please select at least one experience level',
                'locations.required' => 'Please select at least one work location',
                'aadhar.required' => 'Aadhar card is required',
                'aadhar.mimes' => 'Only .pdf, .doc, .docx, .png, .jpg, .jpeg, .webp file types are allowed.',
                'aadhar.max' => 'Aadhaar file size must not exceed 2 MB',
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
                'email' => $request->email,
                'phone' => $request->mobile,
                'role_id' => 3,
                'status' => 1,
            ]);

            // UPLOAD AADHAR
            $aadharPath = $request->file('aadhar')->store('aadhar_files', 'public');

            // Yes/No → 1/0 conversion
            $ownBike = strtolower($request->own_bike) === 'yes' ? 1 : 0;
            $ownPhone = strtolower($request->own_phone) === 'yes' ? 1 : 0;

            // CREATE SUPERVISOR
            $supervisor = Supervisor::create([
                'user_id' => $user->id,
                'address' => $request->address,
                 'salary' => $request->salary,
                'city' => $request->city,
                'state_id' => $request->state_id,
                'pincode' => $request->pincode,
                'own_bike' => $ownBike,
                'own_phone' => $ownPhone,
                'aadhar_file' => $aadharPath,
            ]);

            // SYNC RELATION DATA
            $supervisor->skills()->sync($request->skills);
            $supervisor->experiences()->sync($request->experiences);
            $supervisor->workLocations()->sync($request->locations);

            // Delete used OTP
            OtpValidation::where('phone', $request->mobile)
                ->where('type', 'supervisor_register')
                ->delete();

            // Clear session data
            session()->forget(['verified_registration_phone', 'registration_phone_locked_at']);

            DB::commit();
            
            session()->flash('success', 'Registration completed successfully. You may now log in.');
            
            return response()->json([
                'status' => true,
                'message' => 'Registration completed successfully. You may now log in.',
                'redirect' => route('supervisor.login.view')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());

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