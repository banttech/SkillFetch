<?php

namespace App\Services\PaymentHandlers;

use App\Models\EmployerJobPayment;
use App\Models\PostJob;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class EmployerJobPostingPaymentHandler implements PaymentHandlerInterface
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

                Log::info("Employer Job Payment Auto-Captured: " . $paymentId);
            }
        } catch (\Exception $e) {
            Log::error("Error Auto-Capturing Employer Job Payment: " . $e->getMessage());
        }
    }

    public function handleCaptured(array $data): void
    {
        try {
            $payment = $data['payload']['payment']['entity'];
            $paymentId = $payment['id'];
            $orderId = $payment['order_id'];

            $record = PostJob::where('payment_order_id', $orderId)->first();

            if (!$record) {
                Log::error("No EmployerJobPayment record found for order: " . $orderId);
                return;
            }

            $record->update([
                'transaction_id' => $paymentId,
                'paymentStatus' => 'paid',
                'status' => 1,
                'paidAt' => now(),
            ]);

        

            Log::info("Employer Job Payment Successfully Updated: " . $paymentId);

        } catch (\Exception $e) {
            Log::error("Error updating Employer Job Payment: " . $e->getMessage());
        }
    }

    public function handleFailed(array $data): void
    {
        try {
            $payment = $data['payload']['payment']['entity'];
            $orderId = $payment['order_id'];

           $record = PostJob::where('payment_order_id', $orderId)->first();

            if ($record) {
                $record->update([
                    'paymentStatus' => 'failed',

                ]);

                Log::info("Employer Job Payment marked as failed: " . $orderId);
            }

        } catch (\Exception $e) {
            Log::error("Error handling failed Employer Job Payment: " . $e->getMessage());
        }
    }
}