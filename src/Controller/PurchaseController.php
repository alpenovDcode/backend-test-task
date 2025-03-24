<?php

namespace App\Controller;

use App\Exception\PaymentException;
use App\Service\PurchaseService;
use App\Service\TaxNumberValidatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseController extends AbstractController
{
    private PurchaseService $purchaseService;
    private TaxNumberValidatorService $taxNumberValidator;

    public function __construct(
        PurchaseService $purchaseService,
        TaxNumberValidatorService $taxNumberValidator
    ) {
        $this->purchaseService = $purchaseService;
        $this->taxNumberValidator = $taxNumberValidator;
    }

    #[Route('/purchase', name: 'app_purchase', methods: ['POST'])]
    public function purchase(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Базовая валидация входных данных
        if (!isset($data['product']) || !is_numeric($data['product'])) {
            return new JsonResponse(['error' => 'Invalid product ID'], Response::HTTP_BAD_REQUEST);
        }
        
        if (!isset($data['taxNumber']) || !$this->taxNumberValidator->validate($data['taxNumber'])) {
            return new JsonResponse(['error' => 'Invalid tax number'], Response::HTTP_BAD_REQUEST);
        }
        
        if (!isset($data['paymentProcessor']) || !is_string($data['paymentProcessor'])) {
            return new JsonResponse(['error' => 'Payment processor is required'], Response::HTTP_BAD_REQUEST);
        }
        
        $productId = (int) $data['product'];
        $taxNumber = $data['taxNumber'];
        $paymentProcessor = $data['paymentProcessor'];
        $couponCode = $data['couponCode'] ?? null;
        
        try {
            $result = $this->purchaseService->process($productId, $taxNumber, $paymentProcessor, $couponCode);
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
            // Общая обработка других ошибок
            return new JsonResponse(['error' => 'An unexpected error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 