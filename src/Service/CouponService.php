<?php

namespace App\Service;

use App\Entity\Coupon;

class CouponService
{
    public function getCoupons(): array
    {
        $coupons = [];
        
        $fixedCoupon = new Coupon();
        $fixedCoupon->setCode('D10')
                    ->setValue(10)
                    ->setIsPercentage(false);
        $coupons['D10'] = $fixedCoupon;
        
        $percentCoupon = new Coupon();
        $percentCoupon->setCode('D15')
                      ->setValue(15)
                      ->setIsPercentage(true);
        $coupons['D15'] = $percentCoupon;
        
        return $coupons;
    }
    
    public function findByCode(string $code): ?Coupon
    {
        $coupons = $this->getCoupons();
        
        return $coupons[$code] ?? null;
    }
} 