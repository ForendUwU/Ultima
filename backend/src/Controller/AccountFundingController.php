<?php

namespace App\Controller;

use App\Service\AccountFundingService;
use App\Service\TokenService;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class AccountFundingController extends AbstractController
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly AccountFundingService $accountFundingService
    ) {

    }

    #[Route(
        "/api/fund",
        methods: ['PATCH']
    )]
    #[Tag('AccountFunding')]
    public function fund(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['amount']){
            return new JsonResponse(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $token = $request->headers->get('Authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        try {
            $result = $this->accountFundingService->fund($data['amount'], $decodedToken->login);
        }
        catch (\Exception $e) {
            return new JsonResponse(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return new JsonResponse(
            [
                'message' => $result
            ],
            Response::HTTP_OK
        );
    }
}