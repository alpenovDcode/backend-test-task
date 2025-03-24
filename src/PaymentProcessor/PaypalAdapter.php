<?php

namespace App\PaymentProcessor;

use App\Exception\PaymentException;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalAdapter implements PaymentProcessorInterface
{
    private PaypalPaymentProcessor $paypalProcessor;

    public function __construct(PaypalPaymentProcessor $paypalProcessor)
    {
        $this->paypalProcessor = $paypalProcessor;
    }

    /**
     * @inheritDoc
     */
    public function processPayment(float $amount): bool
    {
        try {
            // PayPal ожидает сумму в минимальных единицах валюты (центы)
            $amountInCents = (int) ($amount * 100);
            $this->paypalProcessor->pay($amountInCents);
            return true;
        } catch (\Exception $e) {
            throw new PaymentException(
                "PayPal payment failed: " . $e->getMessage(),
                PaymentException::ERROR_PAYMENT_FAILED,
                $e
            );
        }
    }
} 