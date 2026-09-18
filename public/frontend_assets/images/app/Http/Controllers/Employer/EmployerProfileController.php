<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\State;
use App\Models\User;
use Exception;

class EmployerProfileController extends Controller
{
    public function profile()
    {
        $user = User::with('employerDetail')->where('id', Auth::id())->first();
        $states = State::all();

        return view('employer.employerProfile.profile', [
            'user' => $user,
            'states' => $states,
            'pageTitle' => 'Employer Edit Profile'
        ]);
    }

    public function updateProfile(Request $request)
    {

        // Validate all fields except phone
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s]+$/'],
            'state' => 'required|string|max:100',
            'pin_code' => ['required', 'digits:6'],
            'image' => 'image|mimes:jpg,png,jpeg|max:2048|dimensions:max_width=200,max_height=200',
            'identity' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'This email is already registered',
            'company_name.required' => 'Company name is required',
            'address.required' => 'Address is required',
            'city.required' => 'City is required',
            'city.regex' => 'City should contain only alphabets',
            'state.required' => 'State is required',
            'pin_code.required' => 'Pin code is required',
            'pin_code.digits' => 'Pin code must be 6 digits',
            'image.image' => 'Profile picture must be an image',
            'image.dimensions' => "Image size can't exceeds the size 200px X 200px",

            'mimes' => 'Please upload valid image. Only JPG, JPEG and PNG extensions are allowed.',

            'image.max' => 'Profile picture size should not exceed 2MB',
            'identity.mimes' => 'Identity proof must be PDF, DOC, or DOCX',
            'identity.max' => 'Identity proof size should not exceed 2MB',
        ]);
        try {
            DB::beginTransaction();

            $user = User::with('employerDetail')->where('id', Auth::id())->first();

            // Update basic info
            $user->name = $request->name;
            $user->email = $request->email;


            // Update profile image
            if ($request->hasFile('image')) {
                // Delete old image if exists
                $oldImagePath = public_path('employe_assets/images/' . $user->image);
                if ($user->image && file_exists($oldImagePath) && $user->image !== 'user-dummy.png') {
                    unlink($oldImagePath);
                }

                $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('employe_assets/images/'), $imageName);
                $user->image = $imageName;
            }





            $user->employerDetail->company_name = $request->company_name;
            $user->employerDetail->address = $request->address;
            $user->employerDetail->city = $request->city;
            $user->employerDetail->state = $request->state;
            $user->employerDetail->pin_code = $request->pin_code;

            // Update identity document
            if ($request->hasFile('identity')) {
                // Delete old identity file if exists
                if ($user->identity && Storage::disk('public')->exists($user->identity)) {
                    Storage::disk('public')->exists($user->employerDetail->identity_proof_path);
                }

                $identityPath = $request->file('identity')->store('identity_documents', 'public');
                $user->employerDetail->identity_proof_path = $identityPath;
            }

            $user->save();
            $user->employerDetail->save();

            DB::commit();

            return back()->with('success', 'Profile updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Employer Profile Update Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while updating profile. Please try again.');
        }
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)
            ->where('id', '!=', Auth::id())
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Email already exists'], 422);
        }

        return response()->json(['message' => 'Email is available']);
    }
}
