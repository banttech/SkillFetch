<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\SupervisorTestFees;
use App\Models\SupervisorTestAttempt;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    /**
     * Initiate Razorpay order.
     *
     * Server-side guards:
     *  - Blocks if supervisor has ANY pending (running) attempt
     *  - Blocks if supervisor is under global lock (under_review / cooldown)
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
        ]);

        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();
        $test       = Test::active()->findOrFail($request->test_id);

        // Guard: block if any test is currently running
        $runningAttempt = SupervisorTestAttempt::where('supervisor_id', $supervisor->id)
            ->where('status', 'pending')
            ->first();

        if ($runningAttempt) {
            return response()->json([
                'success' => false,
                'message' => 'You have an ongoing test in progress. Please complete it before paying for another test.',
            ], 422);
        }

        // Guard: block if under global lock
        $globalBlock = SupervisorTestAttempt::getGlobalBlockForSupervisor($supervisor->id);

        if ($globalBlock && $globalBlock->block_state !== 'free') {
            $msg = $globalBlock->block_state === 'under_review'
                ? 'Your recent test is under admin review. You cannot pay for a new attempt until it is reviewed.'
                : 'You are in a wait period. You cannot pay for a new attempt yet.';

            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 422);
        }

        try {
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $order = $api->order->create([
                'receipt'         => 'sup_' . $supervisor->id . '_' . time(),
                'amount'          => $test->fees * 100,
                'currency'        => 'INR',
                // 'payment_capture' => 1,
                'notes' => [
                'payment_type' => 'supervisor_test_fee',  // KEY IDENTIFIER
                'supervisor_id' => $supervisor->id,
                'test_id' => $test->id,
            ]
            ]);

           

            SupervisorTestFees::create([
                'supervisor_id'           => $supervisor->id,
                'test_id'                 => $test->id,
                'razorpay_order_id'       => $order->id,
                'payment_status'          => 'pending',
                'name'                    => $test->name,
                'fees'                    => $test->fees,
                'timing'                  => $test->timing,
                'marks'                   => 0,
                'question_per_department' => $test->question_per_department,
                'no_of_departments'       => $test->no_of_departments,
                'total_question'          => $test->total_question,
                'total_marks'             => $test->total_marks,
                'passing_marks'           => $test->passing_marks,
            ]);

            return response()->json([
                'success'  => true,
                'key'      => config('services.razorpay.key'),
                'amount'   => $test->fees,
                'orderId'  => $order->id,
                'name'     => $supervisor->user->name  ?? '',
                'email'    => $supervisor->user->email ?? '',
                'test_id'  => $test->id, // used by component to redirect after payment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify Razorpay signature and mark fee as paid.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature'  => 'required',
        ]);

        try {
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);

            $feeRecord = SupervisorTestFees::where('razorpay_order_id', $request->razorpay_order_id)
                ->first();

            // if ($feeRecord) {
            //     $feeRecord->update([
            //         'payment_status' => 'paid',
            //         'transaction_id' => $request->razorpay_payment_id,
            //     ]);
            // }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}