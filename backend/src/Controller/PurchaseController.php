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
    public function purchase(Request $request): ?JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['gameId']){
            return new JsonResponse(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $token = $request->headers->get('Authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        $result = $this->purchaseService->purchase($data['gameId'], $decodedToken->login);

        return new JsonResponse(
            $result['content'],
            $result['code']
        );
    }

    #[Route(
        "/api/user/get-purchased-games",
        methods: ['GET']
    )]
    #[Tag('User')]
    public function getPurchasedGames(Request $request): ?JsonResponse
    {
        $token = $request->headers->get('authorization');

        $result = $this->purchaseService->getPurchasedGames($token);
        return new JsonResponse(
            $result['content']->getValues(),
            $result['code']
        );
    }
}

