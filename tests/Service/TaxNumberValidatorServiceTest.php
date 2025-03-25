<?php

namespace App\Tests\Service;

use App\Service\TaxNumberValidatorService;
use PHPUnit\Framework\TestCase;

class TaxNumberValidatorServiceTest extends TestCase
{
    private TaxNumberValidatorService $taxNumberValidator;

    protected function setUp(): void
    {
        $this->taxNumberValidator = new TaxNumberValidatorService();
    }

    /**
     * @dataProvider validTaxNumbersProvider
     */
    public function testValidTaxNumbers(string $taxNumber): void
    {
        $this->assertTrue($this->taxNumberValidator->validate($taxNumber));
    }

    /**
     * @dataProvider invalidTaxNumbersProvider
     */
    public function testInvalidTaxNumbers(string $taxNumber): void
    {
        $this->assertFalse($this->taxNumberValidator->validate($taxNumber));
    }

    /**
     * @dataProvider countryCodesProvider
     */
    public function testGetCountryCode(string $taxNumber, string $expectedCountryCode): void
    {
        $this->assertEquals($expectedCountryCode, $this->taxNumberValidator->getCountryCode($taxNumber));
    }

    public function validTaxNumbersProvider(): array
    {
        return [
            'German tax number' => ['DE123456789'],
            'Italian tax number' => ['IT12345678900'],
            'Greek tax number' => ['GR123456789'],
            'French tax number' => ['FRAb123456789'],
        ];
    }

    public function invalidTaxNumbersProvider(): array
    {
        return [
            'Empty string' => [''],
            'Invalid country code' => ['XX123456789'],
            'Too short German' => ['DE12345678'],
            'Too long German' => ['DE1234567890'],
            'Too short Italian' => ['IT1234567890'],
            'Too long Italian' => ['IT123456789000'],
            'With special characters' => ['DE123456!89'],
            'With letters in wrong place German' => ['DEa23456789'],
            'Too short Greek' => ['GR12345678'],
            'Too short French' => ['FRAb12345678'],
        ];
    }

    public function countryCodesProvider(): array
    {
        return [
            'German' => ['DE123456789', 'DE'],
            'Italian' => ['IT12345678900', 'IT'],
            'Greek' => ['GR123456789', 'GR'],
            'French' => ['FRAb123456789', 'FR'],
            'Unknown' => ['XX123456789', null],
        ];
    }
} 