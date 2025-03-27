<?php

namespace App\Entity;

class Coupon
{
    public const TYPE_FIXED = 'fixed';
    public const TYPE_PERCENTAGE = 'percentage';

    private ?int $id = null;
    private string $code;
    private string $type;
    private float $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, [self::TYPE_FIXED, self::TYPE_PERCENTAGE])) {
            throw new \InvalidArgumentException('Invalid coupon type');
        }
        
        $this->type = $type;
        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }
    
    public function isPercentage(): bool
    {
        return $this->type === self::TYPE_PERCENTAGE;
    }
    
    public function isFixed(): bool
    {
        return $this->type === self::TYPE_FIXED;
    }
} 