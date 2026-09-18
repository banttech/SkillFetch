<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class AdminProfileController extends Controller
{

    public function edit()
    {
        try {

            $pageTitle = 'Profile';

            return view('admin.adminProfile.adminProfile', compact('pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Unable to load profile page.');
        }
    }

    public function update(Request $request)
    {



        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'image' => 'image|mimes:jpg,png,jpeg|max:2048|dimensions:max_width=200,max_height=200',
        ], [

            'name.required' => 'Name field is required',



            'email.required' => 'Email field is required',

            'email.email' => 'Email is invalid',

            'email.unique' => 'Email Id is already taken',

            // 'phoneNumber.required' => 'Phone Number field is required',

            'image.dimensions' => "Image size can't exceeds the size 200px X 200px",

            'mimes' => 'Please upload valid image. Only JPG, JPEG and PNG extensions are allowed.',



        ]);

        try {

            $user = Auth::user();

            // Update basic details
            $user->name = $request->name;
            $user->email = $request->email;

            // Handle image upload
            if ($request->hasFile('image')) {

                if ($user->image && file_exists(public_path('admin_assets/images/' . $user->image))) {
                    unlink(public_path('admin_assets/images/' . $user->image));
                }

                $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('admin_assets/images/'), $imageName);

                $user->image = $imageName;
            }

            $user->save();

            return redirect()->back()->with('success', 'Profile updated successfully.');
        } catch (Exception $e) {

            return back()->with('error', 'Something went wrong while updating profile.');
        }
    }

    public function editpassword()
    {
        try {

            $pageTitle = 'Update Password';

            return view('admin.adminProfile.editPassword', compact('pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Unable to load password update page.');
        }
    }

    public function updatepassword(Request $request)
    {
      
          
            $request->validate([
                'old_password' => 'required',
                'password' => 'required|min:6',
                'password_confirmation' => 'required|same:password',
            ],[
                'old_password.required' => 'Current password field is required',
                'password.required' => 'New password field is required',
                'password.min' => 'New password must be at least 6 characters',
                'password_confirmation.required' => 'Confirm password field is required',
                'password_confirmation.same' => 'Confirm password must match the new password',
            ]);

              try {

            $user = Auth::user();

            // Check old password
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect.');
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            return back()->with('success', 'Password updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to update password. Try again.');
        }
    }
}
