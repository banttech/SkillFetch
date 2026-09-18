<?php

namespace App\Services\PaymentHandlers;

use App\Models\SupervisorProfileViewPayment;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class EmployerProfileViewPaymentHandler implements PaymentHandlerInterface
{
    public function handleAuthorized(array $data): void
    {
        try {
            $paymentId = $data['payload']['payment']['entity']['id'];
            $amount = $data['payload']['payment']['entity']['amount'];

            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $payment = $api->payment->fetch($paymentId);

            if ($payment->status === "authorized") {
                $payment->capture([
                    'amount' => $amount,
                    'currency' => 'INR'
                ]);

                Log::info("Profile View Payment Auto-Captured: " . $paymentId);
            }
        } catch (\Exception $e) {
            Log::error("Error Auto-Capturing Profile View Payment: " . $e->getMessage());
        }
    }

    public function handleCaptured(array $data): void
    {
        try {
            $payment = $data['payload']['payment']['entity'];
            $paymentId = $payment['id'];
            $orderId = $payment['order_id'];

            $record = SupervisorProfileViewPayment::where('razorpay_order_id', $orderId)->first();

            if (!$record) {
                Log::error("No Profile View Payment found for order: " . $orderId);
                return;
            }

            $record->update([
                'transaction_id' => $paymentId,
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            Log::info("Profile View Payment Successfully Updated: " . $paymentId);

        } catch (\Exception $e) {
            Log::error("Error updating Profile View Payment: " . $e->getMessage());
        }
    }

    public function handleFailed(array $data): void
    {
        try {
            $payment = $data['payload']['payment']['entity'];
            $orderId = $payment['order_id'];

            $record = SupervisorProfileViewPayment::where('razorpay_order_id', $orderId)->first();

            if ($record) {
                $record->update([
                    'payment_status' => 'failed',
                    'failed_at' => now(),
                ]);

                Log::info("Profile View Payment marked as failed: " . $orderId);
            }

        } catch (\Exception $e) {
            Log::error("Error handling failed Profile View Payment: " . $e->getMessage());
        }
    }
}