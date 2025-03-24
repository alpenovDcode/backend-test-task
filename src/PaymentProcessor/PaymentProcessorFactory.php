<?php

namespace App\PaymentProcessor;

use App\Exception\PaymentException;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PaymentProcessorFactory
{
    private PaypalPaymentProcessor $paypalProcessor;
    private StripePaymentProcessor $stripeProcessor;

    public function __construct(
        PaypalPaymentProcessor $paypalProcessor,
        StripePaymentProcessor $stripeProcessor
    ) {
        $this->paypalProcessor = $paypalProcessor;
        $this->stripeProcessor = $stripeProcessor;
    }

    /**
     * Создает адаптер для выбранного платежного процессора
     * 
     * @param string $type Тип платежного процессора ('paypal' или 'stripe')
     * @return PaymentProcessorInterface
     * @throws PaymentException Если указан неизвестный тип
     */
    public function create(string $type): PaymentProcessorInterface
    {
        return match(strtolower($type)) {
            'paypal' => new PaypalAdapter($this->paypalProcessor),
            'stripe' => new StripeAdapter($this->stripeProcessor),
            default => throw new PaymentException(
                "Unknown payment processor: $type",
                PaymentException::ERROR_INVALID_PAYMENT_PROCESSOR
            ),
        };
    }
} 