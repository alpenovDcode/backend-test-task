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
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

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
    #[OA\Post(
        path: '/calculate-price',
        description: 'Расчет цены продукта с учетом налога и купона',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CalculatePriceRequest')
        ),
        tags: ['Price Calculator'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешный расчет цены',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'basePrice', type: 'number', example: 100),
                        new OA\Property(property: 'discountAmount', type: 'number', example: 15),
                        new OA\Property(property: 'priceAfterDiscount', type: 'number', example: 85),
                        new OA\Property(property: 'taxAmount', type: 'number', example: 16.15),
                        new OA\Property(property: 'finalPrice', type: 'number', example: 101.15)
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Ошибка в запросе',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'errors', type: 'object'),
                        new OA\Property(property: 'message', type: 'string', example: 'Validation failed')
                    ]
                )
            )
        ]
    )]
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