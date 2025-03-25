<?php

namespace App\Controller;

use App\DTO\CalculatePriceRequest;
use App\Service\PriceCalculatorService;
use App\Service\ValidationErrorFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PriceController extends AbstractController
{
    private PriceCalculatorService $priceCalculator;
    private ValidatorInterface $validator;
    private ValidationErrorFormatter $errorFormatter;

    public function __construct(
        PriceCalculatorService $priceCalculator,
        ValidatorInterface $validator,
        ValidationErrorFormatter $errorFormatter
    ) {
        $this->priceCalculator = $priceCalculator;
        $this->validator = $validator;
        $this->errorFormatter = $errorFormatter;
    }

    #[Route('/calculate-price', name: 'app_calculate_price', methods: ['POST'])]
    public function calculatePrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }
        
        $calculateRequest = CalculatePriceRequest::fromArray($data);
        
        $violations = $this->validator->validate($calculateRequest);
        if (count($violations) > 0) {
            return new JsonResponse(
                $this->errorFormatter->format($violations),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        
        try {
            $priceData = $this->priceCalculator->calculate(
                $calculateRequest->getProduct(),
                $calculateRequest->getTaxNumber(),
                $calculateRequest->getCouponCode()
            );
            return new JsonResponse($priceData);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
} 