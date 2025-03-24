<?php

namespace App\Service;

use App\Exception\PaymentException;
use App\PaymentProcessor\PaymentProcessorFactory;

class PurchaseService
{
    private PriceCalculatorService $priceCalculator;
    private PaymentProcessorFactory $paymentProcessorFactory;

    public function __construct(
        PriceCalculatorService $priceCalculator,
        PaymentProcessorFactory $paymentProcessorFactory
    ) {
        $this->priceCalculator = $priceCalculator;
        $this->paymentProcessorFactory = $paymentProcessorFactory;
    }

    /**
     * Обработка покупки с использованием выбранного платежного процессора
     *
     * @param int $productId Идентификатор продукта
     * @param string $taxNumber Налоговый номер
     * @param string $paymentProcessor Тип платежного процессора
     * @param string|null $couponCode Код купона (опционально)
     * @return array Результат покупки
     * @throws \InvalidArgumentException Если данные некорректны
     * @throws PaymentException Если возникла ошибка платежа
     */
    public function process(
        int $productId, 
        string $taxNumber, 
        string $paymentProcessor, 
        ?string $couponCode = null
    ): array {
        // Расчет цены
        $priceData = $this->priceCalculator->calculate($productId, $taxNumber, $couponCode);
        
        // Получение платежного процессора
        $processor = $this->paymentProcessorFactory->create($paymentProcessor);
        
        // Обработка платежа
        $success = $processor->processPayment($priceData['finalPrice']);
        
        return [
            'success' => $success,
            'priceData' => $priceData
        ];
    }
} 