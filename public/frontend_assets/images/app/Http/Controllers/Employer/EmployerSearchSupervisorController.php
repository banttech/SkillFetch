<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\PostJob;
use App\Models\Skill;
use App\Models\Supervisor;
use App\Models\SupervisorProfileViewPayment;
use App\Models\WorkLocation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class EmployerSearchSupervisorController extends Controller
{
    const PROFILE_VIEW_FEE = 100; // ₹100 per profile view

    /**
     * Search Supervisors
     */
    public function index(Request $request)
    {
        $pageTitle = 'Search Supervisor';
        $employer = Auth::user()->employerDetail;

        // Start query - only show supervisors who passed test
        $supervisors = Supervisor::with(['user', 'workLocations', 'skills', 'experiences', 'state'])
            ->whereHas('passedTestAttempt');

        $skills = Skill::get();
        $workLocations = WorkLocation::get();

        // Apply search filters
        if ($request->filled('name')) {
            $supervisors->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('city')) {
            $supervisors->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('preferred_location')) {
            $supervisors->whereHas('workLocations', function ($q) use ($request) {
                $q->where('location_id', $request->preferred_location); // Fixed: use ID instead of name
            });
        }

        if ($request->filled('own_bike')) {
            $supervisors->where('own_bike', $request->own_bike);
        }

        if ($request->filled('own_phone')) {
            $supervisors->where('own_phone', $request->own_phone);
        }

        if ($request->filled('skill_tag')) {
            $supervisors->whereHas('skills', function ($q) use ($request) {
                $q->where('skill_id', $request->skill_tag); // Fixed: use ID instead of name
            });
        }

        $supervisors = $supervisors->paginate(10);

        // For each supervisor, check access status
        foreach ($supervisors as $supervisor) {
            // Check if supervisor applied to employer's jobs
            $supervisor->hasApplied = $supervisor->appliedJobs()
                ->where('employer_id', $employer->id)
                ->exists();

            // Check if employer paid for profile
            $supervisor->hasPaid = SupervisorProfileViewPayment::where('employer_id', $employer->id)
                ->where('supervisor_id', $supervisor->id)
                ->where('payment_status', 'paid')
                ->exists();

            // Can view profile for free
            $supervisor->canViewFree = $supervisor->hasApplied || $supervisor->hasPaid;
        }

        return view('employer.searchSupervisor.searchSupervisor', compact('supervisors', 'pageTitle', 'skills', 'workLocations'));
    }

    /**
     * Export Unlocked Supervisors
     */
    public function exportUnlockedSupervisors(Request $request)
    {
        try {
            $employer = Auth::user()->employerDetail;
            
            // Get base query of all passed supervisors
            $supervisorsQuery = Supervisor::with(['user', 'workLocations', 'skills', 'experiences', 'state'])
                ->whereHas('passedTestAttempt');

            // Find supervisors that are unlocked (either applied to job or paid)
            $unlockedSupervisorIds = [];
            
            // 1. Get IDs of supervisors who applied to employer's jobs
            $appliedSupervisorIds = Supervisor::whereHas('appliedJobs', function($q) use ($employer) {
                $q->where('employer_id', $employer->id);
            })->pluck('id')->toArray();
            
            // 2. Get IDs of supervisors whose profile has been paid for
            $paidSupervisorIds = Supervisor::whereHas('profileViewPayments', function($q) use ($employer) {
                $q->where('employer_id', $employer->id)
                  ->where('payment_status', 'paid');
            })->pluck('id')->toArray();
            
            $unlockedSupervisorIds = array_unique(array_merge($appliedSupervisorIds, $paidSupervisorIds));
            
            // Filter only unlocked supervisors
            $supervisorsQuery->whereIn('id', $unlockedSupervisorIds);

            // Apply the same search filters as in index() to the export
            if ($request->filled('name')) {
                $supervisorsQuery->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->name . '%');
                });
            }
            if ($request->filled('city')) {
                $supervisorsQuery->where('city', 'like', '%' . $request->city . '%');
            }
            if ($request->filled('preferred_location')) {
                $supervisorsQuery->whereHas('workLocations', function ($q) use ($request) {
                    $q->where('location_id', $request->preferred_location);
                });
            }
            if ($request->filled('own_bike')) {
                $supervisorsQuery->where('own_bike', $request->own_bike);
            }
            if ($request->filled('own_phone')) {
                $supervisorsQuery->where('own_phone', $request->own_phone);
            }
            if ($request->filled('skill_tag')) {
                $supervisorsQuery->whereHas('skills', function ($q) use ($request) {
                    $q->where('skill_id', $request->skill_tag);
                });
            }

            $supervisors = $supervisorsQuery->get();

            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=unlocked_supervisors.csv",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );
            
            $columns = [
                'Name', 'Email', 'Phone', 'Address', 'City', 'State', 'Pincode', 
                'Own a Bike', 'Own a Phone', 'Skills', 'Experience Areas', 'Preferred Work Locations'
            ];

            $callback = function() use($supervisors, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($supervisors as $sup) {
                    $skills = $sup->skills ? $sup->skills->pluck('name')->implode(', ') : 'N/A';
                    $experiences = $sup->experiences ? $sup->experiences->pluck('name')->implode(', ') : 'N/A';
                    $workLocations = $sup->workLocations ? $sup->workLocations->pluck('name')->implode(', ') : 'N/A';
                    $state = $sup->state ? $sup->state->name : 'N/A';

                    $row = [
                        $sup->user->name ?? 'N/A',
                        $sup->user->email ?? 'N/A',
                        $sup->user->phone ?? 'N/A',
                        $sup->address ?? 'N/A',
                        $sup->city ?? 'N/A',
                        $state,
                        $sup->pincode ?? 'N/A',
                        $sup->own_bike ? 'Yes' : 'No',
                        $sup->own_phone ? 'Yes' : 'No',
                        $skills,
                        $experiences,
                        $workLocations
                    ];

                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->route('employer.search.supervisor')
                ->with('error', 'Unable to export supervisors: ' . $e->getMessage());
        }
    }

    /**
     * View Supervisor Profile
     */
    public function profile($supervisorId)
    {
        $pageTitle = 'Supervisor Profile';
        $employer = Auth::user()->employerDetail;

        $supervisor = Supervisor::with([
            'user',
            'state',
            'workLocations',
            'skills',
            'experiences',
            'passedTestAttempt'
        ])->findOrFail($supervisorId);

        // Paginated Test Attempts History
        $testAttempts = $supervisor->testAttempts()
            ->whereIn('test_submission_status', ['supervisor', 'automatic'])
            ->with(['test', 'testSkills.skill'])
            ->latest('submitted_at')
            ->paginate(5);

        // Check if supervisor passed test
        if (!$supervisor->passedTestAttempt) {
            return redirect()->route('employer.search.supervisor')
                ->with('error', 'This supervisor has not passed the qualification test yet.');
        }

        // Check if supervisor applied to any of employer's jobs
        $hasAppliedToEmployerJob = $supervisor->appliedJobs()
            ->where('employer_id', $employer->id)
            ->exists();

        // Check if employer already paid to view this profile
        $hasPaidForProfile = SupervisorProfileViewPayment::where('employer_id', $employer->id)
            ->where('supervisor_id', $supervisor->id)
            ->where('payment_status', 'paid')
            ->exists();

        // If supervisor applied OR employer paid, show full profile
        if ($hasAppliedToEmployerJob || $hasPaidForProfile) {
            return view('employer.searchSupervisor.supervisor-profile', compact(
                'supervisor',
                'pageTitle',
                'hasAppliedToEmployerJob',
                'hasPaidForProfile',
                'testAttempts'
            ));
        }

        // Otherwise, show payment gate
        return view('employer.searchSupervisor.supervisor-profile-locked', compact(
            'supervisor',
            'pageTitle'
        ));
    }

   
    // public function initiateProfileViewPayment(Request $request)
    // {
    //     $request->validate([
    //         'supervisor_id' => 'required|exists:supervisors,id'
    //     ]);

    //     try {
    //         $employer = Auth::user()->employerDetail;
    //         $supervisorId = $request->supervisor_id;

    //         // Check if supervisor exists and passed test
    //         $supervisor = Supervisor::with('passedTestAttempt')->findOrFail($supervisorId);

    //         if (!$supervisor->passedTestAttempt) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'This supervisor has not passed the qualification test.'
    //             ], 400);
    //         }

    //         // Check if supervisor applied to any job
    //         $hasAppliedToEmployerJob = $supervisor->appliedJobs()
    //             ->where('employer_id', $employer->id)
    //             ->exists();

    //         if ($hasAppliedToEmployerJob) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'This supervisor has already applied to your jobs. No payment needed.'
    //             ], 400);
    //         }

    //         // Check if already paid
    //         $existingPayment = SupervisorProfileViewPayment::where('employer_id', $employer->id)
    //             ->where('supervisor_id', $supervisorId)
    //             ->first();

    //         if ($existingPayment && $existingPayment->payment_status === 'paid') {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'You have already paid to view this profile.'
    //             ], 400);
    //         }

    //         // If pending payment exists, use it
    //         if ($existingPayment && $existingPayment->payment_status === 'pending') {
    //             return response()->json([
    //                 'success' => true,
    //                 'orderId' => $existingPayment->razorpay_order_id,
    //                 'amount' => $existingPayment->amount,
    //                 'key' => config('services.razorpay.key'),
    //                 'name' => Auth::user()->name,
    //                 'email' => Auth::user()->email,
    //             ]);
    //         }

    //         // Create Razorpay Order
    //         $api = new Api(
    //             config('services.razorpay.key'),
    //             config('services.razorpay.secret')
    //         );

    //         $razorpayOrder = $api->order->create([
    //             'receipt' => "PROFILE-VIEW-" . $supervisorId . "-" . time(),
    //             'amount' => self::PROFILE_VIEW_FEE * 100, // Amount in paise
    //             'currency' => 'INR',
    //             'notes' => [
    //                 'payment_type' => 'employer_profile_view',
    //                 'employer_id' => $employer->id,
    //                 'supervisor_id' => $supervisorId,
    //             ]
    //         ]);

    //         // Store pending payment
    //         SupervisorProfileViewPayment::create([
    //             'employer_id' => $employer->id,
    //             'supervisor_id' => $supervisorId,
    //             'razorpay_order_id' => $razorpayOrder['id'],
    //             'payment_status' => 'pending',
    //             'amount' => self::PROFILE_VIEW_FEE,
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'orderId' => $razorpayOrder['id'],
    //             'amount' => self::PROFILE_VIEW_FEE,
    //             'key' => config('services.razorpay.key'),
    //             'name' => Auth::user()->name,
    //             'email' => Auth::user()->email,
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error("Profile View Payment Initiation Error: " . $e->getMessage());

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to initiate payment. Please try again.'
    //         ], 500);
    //     }
    // }


    public function initiateProfileViewPayment(Request $request)
{
    $request->validate([
        'supervisor_id' => 'required|exists:supervisors,id'
    ]);

    try {
        $employer = Auth::user()->employerDetail;
        $supervisorId = $request->supervisor_id;

        // Check if supervisor exists and passed test
        $supervisor = Supervisor::with('passedTestAttempt')->findOrFail($supervisorId);

        if (!$supervisor->passedTestAttempt) {
            return response()->json([
                'success' => false,
                'message' => 'This supervisor has not passed the qualification test.'
            ], 400);
        }

        // Check if supervisor applied to any job
        $hasAppliedToEmployerJob = $supervisor->appliedJobs()
            ->where('employer_id', $employer->id)
            ->exists();

        if ($hasAppliedToEmployerJob) {
            return response()->json([
                'success' => false,
                'message' => 'This supervisor has already applied to your jobs. No payment needed.'
            ], 400);
        }

        // Check if already paid
        $existingPayment = SupervisorProfileViewPayment::where('employer_id', $employer->id)
            ->where('supervisor_id', $supervisorId)
            ->first();

        if ($existingPayment && $existingPayment->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'You have already paid to view this profile.'
            ], 400);
        }

        // ✅ If pending payment exists with order_id, verify it's still valid
        if ($existingPayment && $existingPayment->payment_status === 'pending' && $existingPayment->razorpay_order_id) {
            try {
                // Initialize Razorpay API
                $api = new Api(
                    config('services.razorpay.key'),
                    config('services.razorpay.secret')
                );

                // Fetch existing order from Razorpay
                $existingOrder = $api->order->fetch($existingPayment->razorpay_order_id);

                // If order status is 'created' (not paid/expired), reuse it
                if ($existingOrder->status === 'created') {
                    return response()->json([
                        'success' => true,
                        'orderId' => $existingPayment->razorpay_order_id,
                        'amount' => $existingPayment->amount,
                        'key' => config('services.razorpay.key'),
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                    ]);
                }

                // If order is not in 'created' status, delete old payment record
                // We'll create a new one below
                $existingPayment->delete();

            } catch (\Exception $e) {
                // Order fetch failed (might be invalid/expired/deleted from Razorpay)
                Log::warning("Existing profile view order fetch failed for supervisor {$supervisorId}: " . $e->getMessage());
                
                // Delete old payment record and create new one
                $existingPayment->delete();
            }
        }

        // ✅ Create NEW Razorpay Order (only if no valid existing order)
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        $razorpayOrder = $api->order->create([
            'receipt' => "PROFILE-VIEW-" . $supervisorId . "-" . time(),
            'amount' => self::PROFILE_VIEW_FEE * 100, // Amount in paise
            'currency' => 'INR',
            'notes' => [
                'payment_type' => 'employer_profile_view',
                'employer_id' => $employer->id,
                'supervisor_id' => $supervisorId,
            ]
        ]);

        if (!$razorpayOrder || !isset($razorpayOrder['id'])) {
            throw new Exception("Failed to create Razorpay order.");
        }

        // Store new pending payment
        SupervisorProfileViewPayment::create([
            'employer_id' => $employer->id,
            'supervisor_id' => $supervisorId,
            'razorpay_order_id' => $razorpayOrder['id'],
            'payment_status' => 'pending',
            'amount' => self::PROFILE_VIEW_FEE,
        ]);

        return response()->json([
            'success' => true,
            'orderId' => $razorpayOrder['id'],
            'amount' => self::PROFILE_VIEW_FEE,
            'key' => config('services.razorpay.key'),
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
        ]);

    } catch (\Exception $e) {
        Log::error("Profile View Payment Initiation Error: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to initiate payment. Please try again.'
        ], 500);
    }
}
}
