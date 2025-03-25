<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PriceControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCalculatePriceWithValidData(): void
    {
        $this->client->request(
            'POST',
            '/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'product' => 1,
                'taxNumber' => 'DE123456789',
                'couponCode' => 'D15'
            ])
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('basePrice', $responseData);
        $this->assertArrayHasKey('discountAmount', $responseData);
        $this->assertArrayHasKey('priceAfterDiscount', $responseData);
        $this->assertArrayHasKey('taxAmount', $responseData);
        $this->assertArrayHasKey('finalPrice', $responseData);
        
        $this->assertEquals(100, $responseData['basePrice']);
        $this->assertEquals(15, $responseData['discountAmount']);
        $this->assertEquals(85, $responseData['priceAfterDiscount']);
        $this->assertEquals(16.15, $responseData['taxAmount']);
        $this->assertEquals(101.15, $responseData['finalPrice']);
    }

    public function testCalculatePriceWithInvalidTaxNumber(): void
    {
        $this->client->request(
            'POST',
            '/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'product' => 1,
                'taxNumber' => 'XX123456789',
                'couponCode' => 'D15'
            ])
        );

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertArrayHasKey('taxNumber', $responseData['errors']);
    }

    public function testCalculatePriceWithInvalidProduct(): void
    {
        $this->client->request(
            'POST',
            '/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'product' => 999,
                'taxNumber' => 'DE123456789',
                'couponCode' => 'D15'
            ])
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Product not found', $responseData['error']);
    }

    public function testCalculatePriceWithInvalidCoupon(): void
    {
        $this->client->request(
            'POST',
            '/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'product' => 1,
                'taxNumber' => 'DE123456789',
                'couponCode' => 'INVALID'
            ])
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Invalid coupon code', $responseData['error']);
    }
} 