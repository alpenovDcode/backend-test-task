<?php

namespace App\Service;

class TaxService
{
    private TaxNumberValidatorService $taxNumberValidator;
    
    private const TAX_RATES = [
        'DE' => 19, // Германия - 19%
        'IT' => 22, // Италия - 22%
        'FR' => 20, // Франция - 20%
        'GR' => 24, // Греция - 24%
    ];

    public function __construct(TaxNumberValidatorService $taxNumberValidator)
    {
        $this->taxNumberValidator = $taxNumberValidator;
    }
    
    public function calculateTax(float $price, string $taxNumber): float
    {
        if (!$this->taxNumberValidator->validate($taxNumber)) {
            throw new \InvalidArgumentException('Invalid tax number format');
        }
        
        $countryCode = $this->taxNumberValidator->getCountryCode($taxNumber);
        $taxRate = $this->getTaxRateForCountry($countryCode);
        
        return $price * ($taxRate / 100);
    }
    
    public function getTaxRateForCountry(string $countryCode): float
    {
        if (!isset(self::TAX_RATES[$countryCode])) {
            throw new \InvalidArgumentException("Unknown country code: $countryCode");
        }
        
        return self::TAX_RATES[$countryCode];
    }
} 