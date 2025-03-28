<?php

namespace App\Entity;

use App\Repository\CouponRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    public const TYPE_FIXED = 'fixed';
    public const TYPE_PERCENTAGE = 'percentage';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 10)]
    private ?string $code = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?float $value = null;

    #[ORM\Column]
    private bool $isPercentage = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function isPercentage(): bool
    {
        return $this->isPercentage;
    }

    public function setIsPercentage(bool $isPercentage): static
    {
        $this->isPercentage = $isPercentage;
        return $this;
    }
} 