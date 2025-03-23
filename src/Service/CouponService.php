<?php

namespace App\Service;

use App\Entity\Coupon;

class CouponService
{
    public function getCoupons(): array
    {
        $coupons = [];
        
        // Пример купона с фиксированной скидкой - 10 евро
        $fixedCoupon = new Coupon();
        $fixedCoupon->setCode('D10')
                    ->setType(Coupon::TYPE_FIXED)
                    ->setValue(10);
        $coupons['D10'] = $fixedCoupon;
        
        // Пример купона с процентной скидкой 15%
        $percentCoupon = new Coupon();
        $percentCoupon->setCode('D15')
                      ->setType(Coupon::TYPE_PERCENTAGE)
                      ->setValue(15);
        $coupons['D15'] = $percentCoupon;
        
        return $coupons;
    }
    
    public function findByCode(string $code): ?Coupon
    {
        $coupons = $this->getCoupons();
        
        return $coupons[$code] ?? null;
    }
} 