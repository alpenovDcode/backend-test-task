<?php

namespace App\PaymentProcessor;

use App\Exception\PaymentException;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripeAdapter implements PaymentProcessorInterface
{
    private StripePaymentProcessor $stripeProcessor;

    public function __construct(StripePaymentProcessor $stripeProcessor)
    {
        $this->stripeProcessor = $stripeProcessor;
    }

    public function processPayment(float $amount): bool
    {
        $result = $this->stripeProcessor->processPayment($amount);
        
        if (!$result) {
            throw new PaymentException(
                "Stripe payment failed. Amount might be too small or payment was declined.",
                PaymentException::ERROR_PAYMENT_FAILED
            );
        }
        
        return true;
    }
} 