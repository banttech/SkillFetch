<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function register()
    {
        $pageTitle = "Register";
        return view('frontend.auth.register',compact('pageTitle'));
    }

        public function login()
    {
         $pageTitle = "Login";
        return view('frontend.auth.login',compact('pageTitle'));
    }


}
