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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class SupervisorProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $supervisor = Supervisor::with(['experiences','skills'])->where('user_id', $user->id)->first();

        if (!$supervisor) {
            return redirect()->back()->with('error', 'Supervisor profile not found.');
        }

        return view('supervisor.profile.editProfile', [
            'pageTitle' => 'Supervisor Edit Profile',
            'user' => $user,
            'supervisor' => $supervisor,
            'states' => State::all(),
            'skills' => Skill::all(),
            'locations' => WorkLocation::all(),
            'experiences' => Experience::all(),
        ]);
    }

    public function update(Request $request)
    {

        // Validate all fields except mobile
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'image' => 'nullable|image|mimes:jpg,jpeg,png|dimensions:max_width=200,max_height=200',
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
            'aadhar' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg,webp|max:2048',
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'This email is already registered',
            'image.image' => 'Profile picture must be an image',
            'mimes' => 'Please upload valid image. Only JPG, JPEG and PNG extensions are allowed.',
            'image.dimensions' => "Profile picture size can't exceeds the size 200px X 200px",

            'image.max' => 'Profile picture size should not exceed 2MB',
            'city.regex' => 'City should contain only alphabets',
            'pincode.digits' => 'Pincode must be 6 digits',
            'skills.required' => 'Please select at least one skill',
            'experiences.required' => 'Please select at least one experience level',
            'locations.required' => 'Please select at least one work location',
            'aadhar.mimes' => 'Only .pdf, .doc, .docx, .png, .jpg, .jpeg, .webp file types are allowed.',
            'aadhar.max' => 'Aadhar file size should not exceed 2MB',
        ]);
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $supervisor = Supervisor::where('user_id', $user->id)->first();

            if (!$supervisor) {
                return redirect()->back()->with('error', 'Supervisor profile not found.');
            }

            // UPDATE USER TABLE
            $user->name = $request->name;
            $user->email = $request->email;

            // UPDATE PROFILE IMAGE
            if ($request->hasFile('image')) {
                // Delete old image if exists
                $oldPath = public_path('supervisor_assets/images/' . $user->image);
                if ($user->image && file_exists($oldPath)) {
                    unlink($oldPath);
                }

                $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('supervisor_assets/images/'), $imageName);
                $user->image = $imageName;
            }

            $user->save();

            // UPDATE AADHAR FILE (if new file uploaded)
            $aadharPath = $supervisor->aadhar_file;
            if ($request->hasFile('aadhar')) {
                // Delete old aadhar if exists
                if ($supervisor->aadhar_file && Storage::disk('public')->exists($supervisor->aadhar_file)) {
                    Storage::disk('public')->delete($supervisor->aadhar_file);
                }
                $aadharPath = $request->file('aadhar')->store('aadhar_files', 'public');
            }

            // Yes/No → 1/0 conversion
            $ownBike = strtolower($request->own_bike) === 'yes' ? 1 : 0;
            $ownPhone = strtolower($request->own_phone) === 'yes' ? 1 : 0;

            // UPDATE SUPERVISOR TABLE
            $supervisor->update([
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

            DB::commit();

            return redirect()->back()->with('success', 'Profile updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Profile Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while updating profile.');
        }
    }
}
