<?php

namespace App\Exception;

class PaymentException extends \Exception
{
    // Константы для типов ошибок
    public const ERROR_INVALID_PAYMENT_PROCESSOR = 1;
    public const ERROR_PAYMENT_FAILED = 2;
    
    /**
     * @param string $message Сообщение об ошибке
     * @param int $code Код ошибки
     * @param \Throwable|null $previous Предыдущее исключение
     */
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 