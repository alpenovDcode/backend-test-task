<?php

namespace App\PaymentProcessor;

interface PaymentProcessorInterface
{
    public function processPayment(float $amount): bool;
} 