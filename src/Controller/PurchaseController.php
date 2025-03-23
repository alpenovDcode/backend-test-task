<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseController extends AbstractController
{
    #[Route('/purchase', name: 'app_purchase', methods: ['POST'])]
    public function purchase(Request $request): JsonResponse
    {
        // Временная заготовка для проверки работы эндпоинта
        $data = json_decode($request->getContent(), true);
        
        // TODO: Реализовать валидацию входных данных
        // TODO: Реализовать обработку покупки
        // TODO: Интегрировать платежные процессоры
        
        return new JsonResponse([
            'message' => 'Purchase endpoint is working',
            'received_data' => $data
        ]);
    }
} 