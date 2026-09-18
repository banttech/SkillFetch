<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use App\Models\SupervisorTestFees;
use App\Models\User;

class SupervisorPaymentWebhookController extends Controller
{
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $data = json_decode($payload, true);

        Log::info('Supervisor Payment Webhook Received:', $data);

        // Razorpay Webhook Secret
        $secret = env('RAZORPAY_WEBHOOK_SECRET'); 

        // Verify signature
        $headerSignature = $request->header('X-Razorpay-Signature');
        $generatedSignature = hash_hmac('sha256', $payload, $secret);

        if ($headerSignature !== $generatedSignature) {
            Log::error("Invalid Supervisor Payment Webhook Signature!");
            return response()->json(['status' => 'error'], 400);
        }

        $event = $data['event'] ?? null;

        switch ($event) {

            case 'payment.authorized':
                Log::info("Supervisor Payment Authorized");
                $this->capturePayment($data);
                break;

            case 'payment.captured':
                Log::info("Supervisor Payment Captured");
                $this->markPaymentAsPaid($data);
                break;

            default:
                Log::info("Unhandled Supervisor Payment Webhook Event: " . $event);
        }

        return response()->json(['status' => 'success'], 200);
    }



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

                Log::info("Supervisor Payment Auto-Captured: " . $paymentId);
            }
        } catch (\Exception $e) {
            Log::error("Error Auto-Capturing Supervisor Payment: " . $e->getMessage());
        }
    }

    protected function markPaymentAsPaid($data)
    {
        try {
            $payment = $data['payload']['payment']['entity'];

            $paymentId = $payment['id'];
            $orderId = $payment['order_id'];     
    
            // Correct updated column name
            $record = SupervisorTestFees::where('razorpay_order_id', $orderId)->first();

            if (!$record) {
                Log::error("No SupervisorTestFees record found for razorpay_order_id: " . $orderId);
                return;
            }


            // Update payment record
            $record->update([
                'transaction_id' => $paymentId,
                'payment_status' => 'paid',
            ]);

            Log::info("Supervisor Test Payment Successfully Updated");

        } catch (\Exception $e) {
            Log::error("Error in Supervisor Payment Update: " . $e->getMessage());
        }
    }
}
