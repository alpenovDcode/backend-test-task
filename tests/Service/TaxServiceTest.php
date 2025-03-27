<?php

namespace App\Tests\Service;

use App\Service\TaxNumberValidatorService;
use App\Service\TaxService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TaxServiceTest extends TestCase
{
    private TaxService $taxService;
    private TaxNumberValidatorService $taxNumberValidator;

    protected function setUp(): void
    {
        $this->taxNumberValidator = $this->createMock(TaxNumberValidatorService::class);
        $this->taxService = new TaxService($this->taxNumberValidator);
    }

    #[DataProvider('taxRateDataProvider')]
    public function testGetTaxRateForCountry(string $countryCode, float $expectedRate): void
    {
        $rate = $this->taxService->getTaxRateForCountry($countryCode);
        $this->assertEquals($expectedRate, $rate);
    }

    public function testGetTaxRateForInvalidCountry(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->taxService->getTaxRateForCountry('XX');
    }

    #[DataProvider('taxCalculationDataProvider')]
    public function testCalculateTax(float $price, string $taxNumber, string $countryCode, float $expectedTax): void
    {
        $this->taxNumberValidator->method('validate')
            ->willReturn(true);
        
        $this->taxNumberValidator->method('getCountryCode')
            ->willReturn($countryCode);
        
        $tax = $this->taxService->calculateTax($price, $taxNumber);
        $this->assertEquals($expectedTax, $tax, '', 0.01);
    }

    public function testCalculateTaxWithInvalidTaxNumber(): void
    {
        $this->taxNumberValidator->method('validate')
            ->willReturn(false);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->taxService->calculateTax(100, 'INVALID');
    }

    public static function taxRateDataProvider(): array
    {
        return [
            'Germany' => ['DE', 19.0],
            'Italy' => ['IT', 22.0],
            'France' => ['FR', 20.0],
            'Greece' => ['GR', 24.0],
        ];
    }

    public static function taxCalculationDataProvider(): array
    {
        return [
            'German tax on 100 euros' => [100, 'DE123456789', 'DE', 19.0],
            'Italian tax on 100 euros' => [100, 'IT12345678900', 'IT', 22.0],
            'French tax on 100 euros' => [100, 'FRAb123456789', 'FR', 20.0],
            'Greek tax on 100 euros' => [100, 'GR123456789', 'GR', 24.0],
            'German tax on 85 euros (after discount)' => [85, 'DE123456789', 'DE', 16.15],
            'Italian tax on 90 euros (after discount)' => [90, 'IT12345678900', 'IT', 19.8],
        ];
    }
} 