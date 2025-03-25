<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PurchaseControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testPurchaseWithValidData(): void
    {
        $this->client->request(
            'POST',
            '/purchase',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'product' => 1,
                'taxNumber' => 'DE123456789',
                'couponCode' => 'D15',
                'paymentProcessor' => 'paypal'
            ])
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('success', $responseData);
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('details', $responseData);
        $this->assertArrayHasKey('priceData', $responseData['details']);
        
        $priceData = $responseData['details']['priceData'];
        $this->assertEquals(100, $priceData['basePrice']);
        $this->assertEquals(15, $priceData['discountAmount']);
        $this->assertEquals(85, $priceData['priceAfterDiscount']);
        $this->assertEquals(16.15, $priceData['taxAmount']);
        $this->assertEquals(101.15, $priceData['finalPrice']);
    }

    public function testPurchaseWithInvalidPaymentProcessor(): void
    {
        $this->client->request(
            'POST',
            '/purchase',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'product' => 1,
                'taxNumber' => 'DE123456789',
                'couponCode' => 'D15',
                'paymentProcessor' => 'unknown'
            ])
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('code', $responseData);
        $this->assertEquals(1, $responseData['code']); // ERROR_INVALID_PAYMENT_PROCESSOR
    }

    public function testPurchaseWithMissingRequiredField(): void
    {
        $this->client->request(
            'POST',
            '/purchase',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'product' => 1,
                'taxNumber' => 'DE123456789'
                // Отсутствует обязательное поле paymentProcessor
            ])
        );

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertArrayHasKey('paymentProcessor', $responseData['errors']);
    }

    public function testPurchaseWithStripeLowAmount(): void
    {
        $this->client->request(
            'POST',
            '/purchase',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'product' => 2, // Наушники 20 евро
                'taxNumber' => 'DE123456789',
                'paymentProcessor' => 'stripe'
            ])
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('code', $responseData);
        $this->assertEquals(2, $responseData['code']); // ERROR_PAYMENT_FAILED
    }
} 