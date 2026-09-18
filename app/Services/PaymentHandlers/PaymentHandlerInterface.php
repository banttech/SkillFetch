<?php

namespace App\Services\PaymentHandlers;

interface PaymentHandlerInterface
{
    /**
     * Handle payment authorized event
     */
    public function handleAuthorized(array $data): void;

    /**
     * Handle payment captured event
     */
    public function handleCaptured(array $data): void;

    /**
     * Handle payment failed event
     */
    public function handleFailed(array $data): void;
}