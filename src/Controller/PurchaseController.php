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