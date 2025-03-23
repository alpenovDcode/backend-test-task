<?php

namespace App\Controller;

use App\Service\PriceCalculatorService;
use App\Service\TaxNumberValidatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PriceController extends AbstractController
{
    private PriceCalculatorService $priceCalculator;
    private TaxNumberValidatorService $taxNumberValidator;

    public function __construct(
        PriceCalculatorService $priceCalculator,
        TaxNumberValidatorService $taxNumberValidator
    ) {
        $this->priceCalculator = $priceCalculator;
        $this->taxNumberValidator = $taxNumberValidator;
    }

    #[Route('/calculate-price', name: 'app_calculate_price', methods: ['POST'])]
    public function calculatePrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Базовая валидация входных данных
        if (!isset($data['product']) || !is_numeric($data['product'])) {
            return new JsonResponse(['error' => 'Invalid product ID'], Response::HTTP_BAD_REQUEST);
        }
        
        if (!isset($data['taxNumber']) || !$this->taxNumberValidator->validate($data['taxNumber'])) {
            return new JsonResponse(['error' => 'Invalid tax number'], Response::HTTP_BAD_REQUEST);
        }
        
        $productId = (int) $data['product'];
        $taxNumber = $data['taxNumber'];
        $couponCode = $data['couponCode'] ?? null;
        
        try {
            $priceData = $this->priceCalculator->calculate($productId, $taxNumber, $couponCode);
            return new JsonResponse($priceData);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
} 