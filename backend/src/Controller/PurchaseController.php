<?php

namespace App\Controller;

use App\Service\PurchaseService;
use App\Service\TokenService;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class PurchaseController extends AbstractController
{
    public function __construct(
        private readonly PurchaseService $purchaseService
    ) {

    }

    #[Route(
        "/api/user/{userId}/purchase-game",
        methods: ['POST']
    )]
    #[Tag('Purchase')]
    public function purchase(Request $request, $userId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['gameId']){
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $result = $this->purchaseService->purchase($data['gameId'], $userId);
        } catch (\Exception $e) {
            return $this->json(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return $this->json(
            [
                'message' => $result
            ],
            Response::HTTP_OK
        );
    }

    #[Route(
        '/api/user/{userId}/purchased-games',
        methods: ['GET']
    )]
    #[Tag('Purchase')]
    public function getUsersPurchasedGames(Request $request, $userId): ?JsonResponse
    {
//        $token = $request->headers->get('authorization');
//        $decodedToken = $this->tokenService->decodeLongToken($token);

        $result = $this->purchaseService->getPurchasedGames($userId);

        return $this->json(
            $result,
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/purchased-games",
        methods: ['DELETE']
    )]
    #[Tag('Purchase')]
    public function deletePurchasedGame(Request $request): ?JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['purchasedGameId'])) {
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->purchaseService->deletePurchasedGame($data['purchasedGameId']);
        } catch (\Exception $e) {
            return $this->json(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return $this->json(
            [
                'message' => 'Successfully deleted'
            ],
            Response::HTTP_OK
        );
    }
}

