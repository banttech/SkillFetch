<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Homecontroller extends Controller
{
   public function index(){
    $pageTitle = '';
    return view('frontend.index',compact('pageTitle'));
   }

   public function privacy(){
     $pageTitle = 'Privacy Policy';
    return view('frontend.policies.privacy',compact('pageTitle'));
   }

    public function termsConditions(){
          $pageTitle = 'Terms & Conditions';
    return view('frontend.policies.termsConditions',compact('pageTitle'));
   }

   public function contactUs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:1000',
        ],[
           
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Contact::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your message has been sent successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
}
