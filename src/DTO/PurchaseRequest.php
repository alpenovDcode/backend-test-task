<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\MapRequestPayload;
use App\Validator\Constraints as CustomAssert;

#[MapRequestPayload]
class PurchaseRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $product = null;

    #[Assert\NotNull]
    #[Assert\Regex(
        pattern: '/^(DE|IT|GR|FR)[A-Z0-9]+$/',
        message: 'Invalid tax number format'
    )]
    public ?string $taxNumber = null;

    public ?string $couponCode = null;

    #[Assert\NotNull]
    #[Assert\Choice(choices: ['paypal', 'stripe'])]
    public ?string $paymentProcessor = null;

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