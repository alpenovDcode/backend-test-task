<?php

namespace App\Service;

use App\Entity\Coupon;
use App\Entity\Product;

class PriceCalculatorService
{
    private ProductService $productService;
    private CouponService $couponService;
    private TaxService $taxService;
    
    public function __construct(
        ProductService $productService,
        CouponService $couponService,
        TaxService $taxService
    ) {
        $this->productService = $productService;
        $this->couponService = $couponService;
        $this->taxService = $taxService;
    }
    
    public function calculate(int $productId, string $taxNumber, ?string $couponCode = null): array
    {
        $product = $this->productService->findById($productId);
        
        if (!$product) {
            throw new \InvalidArgumentException('Product not found');
        }
        
        $basePrice = $product->getPrice();
        $discountedPrice = $basePrice;
        $discountAmount = 0;
        
        if ($couponCode) {
            $coupon = $this->couponService->findByCode($couponCode);
            
            if (!$coupon) {
                throw new \InvalidArgumentException('Invalid coupon code');
            }
            
            $discountAmount = $this->calculateDiscount($basePrice, $coupon);
            $discountedPrice = $basePrice - $discountAmount;
        }
        
        $taxAmount = $this->taxService->calculateTax($discountedPrice, $taxNumber);
        $finalPrice = $discountedPrice + $taxAmount;
        
        return [
            'basePrice' => $basePrice,
            'discountAmount' => $discountAmount,
            'priceAfterDiscount' => $discountedPrice,
            'taxAmount' => $taxAmount,
            'finalPrice' => $finalPrice
        ];
    }
    
    private function calculateDiscount(float $basePrice, Coupon $coupon): float
    {
        if ($coupon->isFixed()) {
            return min($basePrice, $coupon->getValue());
        } elseif ($coupon->isPercentage()) {
            return $basePrice * ($coupon->getValue() / 100);
        }
        
        return 0;
    }
} 