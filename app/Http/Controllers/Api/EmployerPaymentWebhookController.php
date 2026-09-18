<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use App\Models\EmployerJobPayment;
use App\Models\PostJob;

class EmployerPaymentWebhookController extends Controller
{
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $data = json_decode($payload, true);

        Log::info('Employer Payment Webhook Received:', $data);

        // Razorpay Webhook Secret
        $secret = env('RAZORPAY_WEBHOOK_SECRET');

        // Verify Signature
        $headerSignature = $request->header('X-Razorpay-Signature');
        $generatedSignature = hash_hmac('sha256', $payload, $secret);

        if ($headerSignature !== $generatedSignature) {
            Log::error("Invalid Employer Payment Webhook Signature!");
            return response()->json(['status' => 'error'], 400);
        }

        $event = $data['event'] ?? null;

        switch ($event) {

            case 'payment.authorized':
                Log::info("Employer Payment Authorized");
                $this->capturePayment($data);
                break;

            case 'payment.captured':
                Log::info("Employer Payment Captured");
                $this->markPaymentAsPaid($data);
                break;

            default:
                Log::info("Unhandled Employer Payment Event: " . $event);
        }

        return response()->json(['status' => 'success'], 200);
    }


    // AUTO-CAPTURE PAYMENT
    protected function capturePayment($data)
    {
        try {
            $paymentId = $data['payload']['payment']['entity']['id'];
            $amount = $data['payload']['payment']['entity']['amount'];

            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $payment = $api->payment->fetch($paymentId);

            if ($payment->status === "authorized") {
                $payment->capture([
                    'amount' => $amount,
                    'currency' => 'INR'
                ]);

                Log::info("Employer Payment Auto-Captured: " . $paymentId);
            }

        } catch (\Exception $e) {
            Log::error("Error Auto-Capturing Employer Payment: " . $e->getMessage());
        }
    }


    // UPDATE EMPLOYER PAYMENT AS PAID
    protected function markPaymentAsPaid($data)
    {
        try {
            $payment = $data['payload']['payment']['entity'];

            $paymentId = $payment['id'];
            $orderId = $payment['order_id'];

            // Fetch employer payment table row
            $record = EmployerJobPayment::where('payment_order_id', $orderId)->first();

            if (!$record) {
                Log::error("EmployerJobPayment not found for order_id: " . $orderId);
                return;
            }

            // Update payment record
            $record->update([
                'transaction_id' => $paymentId,
                'payment_status' => 'paid'
            ]);

            Log::info("Employer Job Payment Updated Successfully → PAID");

        } catch (\Exception $e) {
            Log::error("Error in Employer Payment Update: " . $e->getMessage());
        }
    }
}
