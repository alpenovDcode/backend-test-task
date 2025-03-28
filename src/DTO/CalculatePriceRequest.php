<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\MapRequestPayload;

#[MapRequestPayload]
class CalculatePriceRequest
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

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->product = $data['product'] ?? null;
        $request->taxNumber = $data['taxNumber'] ?? null;
        $request->couponCode = $data['couponCode'] ?? null;
        return $request;
    }

    public function getProduct(): ?int
    {
        return $this->product;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }
} 