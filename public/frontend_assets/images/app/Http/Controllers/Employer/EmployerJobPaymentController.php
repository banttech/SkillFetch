<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\EmployerJobPayment;
use App\Models\PostJob;
use Razorpay\Api\Api;
use Illuminate\Http\Request;

class EmployerJobPaymentController extends Controller
{
    public function createOrder(Request $request)
    {

        $job_id = $request->input('job_id') ?? $request->json('job_id');

        if (!$job_id) {
            return response()->json([
                'success' => false,
                'message' => 'job_id missing'
            ]);
        }

        // Ensure job exists
        $job = PostJob::findOrFail($job_id);

        $payment = EmployerJobPayment::firstOrCreate(
            ['job_id' => $job_id],
            [
                'payment_status' => 'unpaid',
                'transaction_id' => null,
                'payment_order_id' => null
            ]
        );

        // Razorpay API Init
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // Create Razorpay Order
        $order = $api->order->create([
            'receipt' => 'JOBPAY_' . $job_id,
            'amount' => 50000, // ₹500
            'currency' => 'INR'
        ]);

        // Save generated Razorpay order_id
        $payment->update([
            'payment_order_id' => $order['id']
        ]);

        return response()->json([
            'success' => true,
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'key' => env('RAZORPAY_KEY'),
            'job_id' => $job_id
        ]);
    }
}
