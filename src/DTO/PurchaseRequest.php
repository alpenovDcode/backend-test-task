<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

class PurchaseRequest
{
    #[Assert\NotBlank(message: "Product ID is required")]
    #[Assert\Type(type: "integer", message: "Product ID must be an integer")]
    #[Assert\Positive(message: "Product ID must be positive")]
    private $product;

    #[Assert\NotBlank(message: "Tax number is required")]
    #[CustomAssert\TaxNumber]
    private $taxNumber;

    #[Assert\Type(type: "string", message: "Coupon code must be a string")]
    private $couponCode;

    #[Assert\NotBlank(message: "Payment processor is required")]
    #[Assert\Choice(choices: ["paypal", "stripe"], message: "Invalid payment processor. Use 'paypal' or 'stripe'")]
    private $paymentProcessor;

    public function getProduct(): int
    {
        return (int) $this->product;
    }

    public function setProduct($product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getTaxNumber(): string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber($taxNumber): self
    {
        $this->taxNumber = $taxNumber;
        return $this;
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function setCouponCode($couponCode): self
    {
        $this->couponCode = $couponCode;
        return $this;
    }

    public function getPaymentProcessor(): string
    {
        return $this->paymentProcessor;
    }

    public function setPaymentProcessor($paymentProcessor): self
    {
        $this->paymentProcessor = $paymentProcessor;
        return $this;
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->setProduct($data['product'] ?? null);
        $dto->setTaxNumber($data['taxNumber'] ?? null);
        $dto->setCouponCode($data['couponCode'] ?? null);
        $dto->setPaymentProcessor($data['paymentProcessor'] ?? null);
        
        return $dto;
    }
} 