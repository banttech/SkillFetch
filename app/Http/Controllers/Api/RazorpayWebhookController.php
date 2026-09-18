<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentHandlers\SupervisorTestPaymentHandler;
use App\Services\PaymentHandlers\EmployerJobPostingPaymentHandler;
use App\Services\PaymentHandlers\EmployerProfileViewPaymentHandler;

class RazorpayWebhookController extends Controller
{
    protected $handlers = [];

    public function __construct()
    {
        // Register all payment handlers
        $this->handlers = [
            'supervisor_test_fee' => SupervisorTestPaymentHandler::class,
            'employer_job_posting' => EmployerJobPostingPaymentHandler::class,
            'employer_profile_view' => EmployerProfileViewPaymentHandler::class,
        ];
    }

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $data = json_decode($payload, true);

        Log::info('Razorpay Webhook Received:', $data);

        // Verify webhook signature
        if (!$this->verifySignature($request, $payload)) {
            Log::error("Invalid Razorpay Webhook Signature!");
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        $event = $data['event'] ?? null;

        // Route based on event type
        switch ($event) {
            case 'payment.authorized':
                $this->handlePaymentAuthorized($data);
                break;

            case 'payment.captured':
                $this->handlePaymentCaptured($data);
                break;

            case 'payment.failed':
                $this->handlePaymentFailed($data);
                break;

            default:
                Log::info("Unhandled Webhook Event: " . $event);
        }

        return response()->json(['status' => 'success'], 200);
    }

    protected function verifySignature(Request $request, string $payload): bool
    {
        $secret = config('services.razorpay.webhook_secret');
        $headerSignature = $request->header('X-Razorpay-Signature');
        $generatedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($generatedSignature, $headerSignature);
    }

    protected function handlePaymentAuthorized(array $data)
    {
        Log::info("Payment Authorized Event");
        
        $handler = $this->getHandler($data);
        if ($handler) {
            $handler->handleAuthorized($data);
        }
    }

    protected function handlePaymentCaptured(array $data)
    {
        Log::info("Payment Captured Event");
        
        $handler = $this->getHandler($data);
        if ($handler) {
            $handler->handleCaptured($data);
        }
    }

    protected function handlePaymentFailed(array $data)
    {
        Log::info("Payment Failed Event");
        
        $handler = $this->getHandler($data);
        if ($handler) {
            $handler->handleFailed($data);
        }
    }

    protected function getHandler(array $data)
    {
        // Extract payment type from order notes
        $orderId = $data['payload']['payment']['entity']['order_id'] ?? null;
        
        if (!$orderId) {
            Log::error("Order ID not found in webhook payload");
            return null;
        }

        // Fetch the payment type from notes
        $paymentType = $this->getPaymentTypeFromOrder($orderId);

        if (!$paymentType || !isset($this->handlers[$paymentType])) {
            Log::error("Unknown payment type: " . $paymentType);
            return null;
        }

        $handlerClass = $this->handlers[$paymentType];
        return new $handlerClass();
    }

    protected function getPaymentTypeFromOrder(string $orderId): ?string
    {
        try {
            $api = new \Razorpay\Api\Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $order = $api->order->fetch($orderId);
            return $order->notes->payment_type ?? null;

        } catch (\Exception $e) {
            Log::error("Error fetching order details: " . $e->getMessage());
            return null;
        }
    }
}