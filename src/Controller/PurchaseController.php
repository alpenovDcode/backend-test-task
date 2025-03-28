<?php

namespace App\Controller;

use App\DTO\PurchaseRequest;
use App\Exception\PaymentException;
use App\Service\PurchaseService;
use App\Service\ValidationErrorFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class PurchaseController extends AbstractController
{
    private PurchaseService $purchaseService;
    private ValidatorInterface $validator;
    private ValidationErrorFormatter $errorFormatter;

    public function __construct(
        PurchaseService $purchaseService,
        ValidatorInterface $validator,
        ValidationErrorFormatter $errorFormatter
    ) {
        $this->purchaseService = $purchaseService;
        $this->validator = $validator;
        $this->errorFormatter = $errorFormatter;
    }

    #[Route('/purchase', name: 'app_purchase', methods: ['POST'])]
    #[OA\Post(
        path: '/purchase',
        description: 'Обработка покупки продукта',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PurchaseRequest')
        ),
        tags: ['Purchase'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешная покупка',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment processed successfully'),
                        new OA\Property(
                            property: 'priceData',
                            properties: [
                                new OA\Property(property: 'basePrice', type: 'number', example: 100),
                                new OA\Property(property: 'discountAmount', type: 'number', example: 15),
                                new OA\Property(property: 'priceAfterDiscount', type: 'number', example: 85),
                                new OA\Property(property: 'taxAmount', type: 'number', example: 16.15),
                                new OA\Property(property: 'finalPrice', type: 'number', example: 101.15)
                            ],
                            type: 'object'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Ошибка в запросе или обработке платежа',
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
    public function purchase(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }
        
        $purchaseRequest = PurchaseRequest::fromArray($data);
        
        $violations = $this->validator->validate($purchaseRequest);
        if (count($violations) > 0) {
            return new JsonResponse(
                $this->errorFormatter->format($violations),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        
        try {
            $result = $this->purchaseService->process(
                $purchaseRequest->getProduct(),
                $purchaseRequest->getTaxNumber(),
                $purchaseRequest->getPaymentProcessor(),
                $purchaseRequest->getCouponCode()
            );
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Payment processed successfully',
                'details' => $result
            ]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (PaymentException $e) {
            return new JsonResponse([
                'error' => 'Payment processing failed: ' . $e->getMessage(),
                'code' => $e->getCode()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'An unexpected error occurred: ' . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
} 