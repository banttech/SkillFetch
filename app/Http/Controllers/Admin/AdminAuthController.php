<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Exception;

class AdminAuthController extends Controller
{
    public function loginView()
    {
        return view('admin.auth.login');
    }



    public function loginSubmit(Request $request)
    {


        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {


            $credentials = $request->only('email', 'password');


            if (Auth::attempt($credentials)) {
                if (Auth::user()->role_id == '1') {
                    return redirect()->route('admin.supervisor.supervisors-List');
                } else {
                    Auth::logout();
                    return redirect()->back()->with('error', 'Invalid Credentials');
                }
            }

            return redirect()->back()->with('error', 'Invalid Credentials');
        } catch (Exception $e) {
           
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }

   

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login.view')->with('success', 'Logged out successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Logout failed. Try again.');
        }
    }

    // Forgot Password View
    public function forgotPasswordView()
    {
        return view('admin.auth.forgotPassword');
    }

    public function forgotPasswordSubmit(Request $request)
    {
       

            $request->validate([
                'email' => 'required|email'
            ]);
 try {
            $user = User::where('email', $request->email)
                ->where('role_id', 1)
                ->first();

            if (!$user) {
                return back()->with('error', "No admin account is associated with this email address.");
            }

            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );

            // Mail::send('admin.email.resetPassword', [
            //     'token' => $token,
            //     'name' => $user->name,
            //     'email' => $user->email
            // ], function ($message) use ($request) {
            //     $message->to($request->email);
            //     $message->subject('Reset Password');
            // });

            return redirect()->route('admin.resetPassword.view',$token);
        } catch (Exception $e) {
            return back()->with('error');
        }
    }

    public function resetPasswordView($token)
    {
        $resetPassword = DB::table('password_reset_tokens')->where('token', $token)->first();

        if (!$resetPassword) {

            return redirect()->route('admin.forgot.password.view')->with('error', 'Invalid token. Please try again.');
        }
        return view('admin.auth.resetPassword', ['token' => $token]);
    }

    public function resetPasswordSubmit(Request $request)
    {


        $request->validate([
            // 'password' => 'required|regex:/[@$!%*#?&]/|regex:/[A-Z]/|min:6',
            'password_confirmation' => 'required|same:password',
        ], [
            'password.required' => 'New Password field is required',
            'password_confirmation.required' => 'Confirm Password field is required',
            'password_confirmation.same' => 'New Password and Confirm Password must be same',
            'password.regex' => 'Password must have at least 8 characters and contains uppercase letters, lowercase letters, numbers, and special characters.',
        ]);
        try {

            $reset = DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->first();

            if (!$reset) {
                return back()->with('error', 'Invalid or expired reset link.');
            }

            $user = User::where('email', $reset->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            DB::table('password_reset_tokens')->where('token', $request->token)->delete();

            return redirect()->route('admin.login.view')->with('success', 'Password updated successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to reset password. Try again.');
        }
    }
}
