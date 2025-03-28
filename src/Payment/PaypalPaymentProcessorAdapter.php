<?php

namespace App\Payment;

use App\Exception\PaymentFailedException;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalPaymentProcessorAdapter implements PaymentProcessorInterface
{
    public function __construct(
        private readonly PaypalPaymentProcessor $processor
    ) {
    }

    public function process(float $amount): void
    {
        try {
            $this->processor->pay($amount);
        } catch (\Exception $e) {
            throw new PaymentFailedException('PayPal payment failed: ' . $e->getMessage());
        }
    }
} 