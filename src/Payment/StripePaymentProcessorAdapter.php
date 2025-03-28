<?php

namespace App\Payment;

use App\Exception\PaymentFailedException;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripePaymentProcessorAdapter implements PaymentProcessorInterface
{
    public function __construct(
        private readonly StripePaymentProcessor $processor
    ) {
    }

    public function process(float $amount): void
    {
        try {
            $this->processor->processPayment($amount);
        } catch (\Exception $e) {
            throw new PaymentFailedException('Stripe payment failed: ' . $e->getMessage());
        }
    }
} 