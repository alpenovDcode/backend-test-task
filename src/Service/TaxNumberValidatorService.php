<?php

namespace App\Service;

class TaxNumberValidatorService
{
    public function validate(string $taxNumber): bool
    {
        $countryCode = substr($taxNumber, 0, 2);
        
        return match ($countryCode) {
            'DE' => $this->validateGermanTaxNumber($taxNumber),
            'IT' => $this->validateItalianTaxNumber($taxNumber),
            'GR' => $this->validateGreekTaxNumber($taxNumber),
            'FR' => $this->validateFrenchTaxNumber($taxNumber),
            default => false,
        };
    }
    
    private function validateGermanTaxNumber(string $taxNumber): bool
    {
        return preg_match('/^DE\d{9}$/', $taxNumber) === 1;
    }
    
    private function validateItalianTaxNumber(string $taxNumber): bool
    {
        return preg_match('/^IT\d{11}$/', $taxNumber) === 1;
    }
    
    private function validateGreekTaxNumber(string $taxNumber): bool
    {
        return preg_match('/^GR\d{9}$/', $taxNumber) === 1;
    }
    
    private function validateFrenchTaxNumber(string $taxNumber): bool
    {
        return preg_match('/^FR[A-Za-z]{2}\d{9}$/', $taxNumber) === 1;
    }
    
    public function getCountryCode(string $taxNumber): ?string
    {
        $countryCode = substr($taxNumber, 0, 2);
        
        if (!in_array($countryCode, ['DE', 'IT', 'GR', 'FR'])) {
            return null;
        }
        
        return $countryCode;
    }
} 