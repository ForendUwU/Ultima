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
        private readonly PurchaseService $purchaseService,
        private readonly TokenService $tokenService
    ) {

    }

    #[Route(
        "/api/purchase-game",
        methods: ['POST']
    )]
    #[Tag('Purchase')]
    public function purchase(Request $request): JsonResponse
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

        $token = $request->headers->get('Authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        try {
            $result = $this->purchaseService->purchase($data['gameId'], $decodedToken->login);
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
        "/api/purchase-game",
        methods: ['GET']
    )]
    #[Tag('Purchase')]
    public function getPurchasedGames(Request $request): ?JsonResponse
    {
        $token = $request->headers->get('authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        $result = $this->purchaseService->getPurchasedGames($decodedToken->login);

        return $this->json(
            $result,
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/purchase-game",
        methods: ['DELETE']
    )]
    #[Tag('Purchase')]
    public function deletePurchasedGames(Request $request): ?JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['gameId'])) {
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $token = $request->headers->get('Authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        try {
            $this->purchaseService->deletePurchasedGame($data['gameId'], $decodedToken->login);
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

