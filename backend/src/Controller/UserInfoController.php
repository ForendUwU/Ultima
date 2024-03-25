<?php

namespace App\Controller;

use App\Service\TokenService;
use App\Service\UserInfoService;
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
        "/api/user/me",
        methods: ['GET']
    )]
    #[Tag('User')]
    public function getUserInfo(Request $request): ?JsonResponse
    {
        $token = $request->headers->get('authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        $result = $this->userInfoService->getUserInfo($decodedToken->id);

        return $this->json(
            $result,
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/user/most-played-games",
        methods: ['GET']
    )]
    #[Tag('User')]
    public function getUsersMostPlayedGames(Request $request): JsonResponse
    {
        $token = $request->headers->get('authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        try {
            $result = $this->userInfoService->getUsersMostPlayedGames($decodedToken->id);
        } catch (\Exception $e) {
            return $this->json(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return $this->json(
            $result,
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/user/change-data",
        methods: ['POST']
    )]
    #[Tag('User')]
    public function updateUserInfo(Request $request): JsonResponse
    {
        $token = $request->headers->get('authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->userInfoService->updateUserInfo($decodedToken->id, $data);

        return $this->json(
            [
                'result' => 'Successfully updated'
            ], Response::HTTP_OK
        );
    }

    #[Route(
        "/api/user/{userId}/change-pass",
        methods: ['PATCH']
    )]
    #[Tag('User')]
    public function updatePassword(Request $request, $userId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['oldPassword'] || !$data['newPassword']){
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->userInfoService->updatePassword($userId, $data['oldPassword'], $data['newPassword']);
        } catch (\Exception $e) {
            return $this->json(
                [
                    'message' => $e->getMessage()
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        return $this->json(
            [
                'message' => 'successfully updated'
            ], Response::HTTP_OK
        );
    }
}
