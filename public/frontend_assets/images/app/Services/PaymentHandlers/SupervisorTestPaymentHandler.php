<?php

namespace App\Services\PaymentHandlers;

use App\Models\SupervisorTestFees;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class SupervisorTestPaymentHandler implements PaymentHandlerInterface
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

                Log::info("Supervisor Test Payment Auto-Captured: " . $paymentId);
            }
        } catch (\Exception $e) {
            Log::error("Error Auto-Capturing Supervisor Test Payment: " . $e->getMessage());
        }
    }

    public function handleCaptured(array $data): void
    {
        try {
            $payment = $data['payload']['payment']['entity'];
            $paymentId = $payment['id'];
            $orderId = $payment['order_id'];

            $record = SupervisorTestFees::where('razorpay_order_id', $orderId)->first();

            if (!$record) {
                Log::error("No SupervisorTestFees record found for order: " . $orderId);
                return;
            }

            $record->update([
                'transaction_id' => $paymentId,
                'payment_status' => 'paid',
            ]);

            Log::info("Supervisor Test Payment Successfully Updated: " . $paymentId);

        } catch (\Exception $e) {
            Log::error("Error updating Supervisor Test Payment: " . $e->getMessage());
        }
    }

    public function handleFailed(array $data): void
    {
        try {
            $payment = $data['payload']['payment']['entity'];
            $orderId = $payment['order_id'];

            $record = SupervisorTestFees::where('razorpay_order_id', $orderId)->first();

            if ($record) {
                $record->update([
                    'payment_status' => 'failed',
                    'failed_at' => now(),
                ]);

                Log::info("Supervisor Test Payment marked as failed: " . $orderId);
            }

        } catch (\Exception $e) {
            Log::error("Error handling failed Supervisor Test Payment: " . $e->getMessage());
        }
    }
}