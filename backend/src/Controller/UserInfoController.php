<?php

namespace App\Controller;

use App\Services\TokenService;
use App\Services\UserInfoService;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

#[AsController]
class UserInfoController extends AbstractController
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly UserInfoService $userInfoService
    ) {

    }
    #[Route(
        "/api/user-info-by-token",
        methods: ['GET']
    )]
    #[Tag('User')]
    public function getUserInfo(Request $request): ?JsonResponse
    {
        $token = $request->headers->get('authorization');

        if (!$token) {
            return new JsonResponse(
                [
                    'message' => 'Token is missing'
                ],
                Response::HTTP_BAD_REQUEST);
        }

        $decodedToken = $this->tokenService->decode($token);
        $result = $this->userInfoService->getUserInfo($decodedToken->login);

        return new JsonResponse($result['content'], Response::HTTP_OK);
    }
}
