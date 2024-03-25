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
        private readonly AccountFundingService $accountFundingService,
        private readonly TokenService $tokenService
    ) {

    }

    #[Route(
        "/api/user/fund",
        methods: ['POST']
    )]
    #[Tag('AccountFunding')]
    public function fund(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $token = $request->headers->get('authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        if (!$data || !$data['amount']){
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $result = $this->accountFundingService->fund($data['amount'], $decodedToken->id);
        }
        catch (\Exception $e) {
            return $this->json(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return $this->json(
            [
                'newAmount' => $result
            ],
            Response::HTTP_OK
        );
    }
}
