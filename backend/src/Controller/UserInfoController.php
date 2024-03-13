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
        $result = $this->userInfoService->getUserInfo($decodedToken->login);

        return new JsonResponse(
            $result,
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/user/get-most-played-games",
        methods: ['GET']
    )]
    #[Tag('User')]
    public function getUsersMostPlayedGames(Request $request): JsonResponse
    {
        $token = $request->headers->get('authorization');

        $decodedToken = $this->tokenService->decodeLongToken($token);
        try {
            $result = $this->userInfoService->getUsersMostPlayedGames($decodedToken->login);
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return new JsonResponse(
            $result,
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/user/check-pass",
        methods: ['POST']
    )]
    #[Tag('User')]
    public function validatePassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['password']){
            return new JsonResponse(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $token = $request->headers->get('authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        try {
            $result = $this->userInfoService->validatePassword($decodedToken->login, $data['password']);
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return $result ?
            new JsonResponse(
                ['result' => 'valid'],
                Response::HTTP_OK
            ) : new JsonResponse(
                ['result' => 'invalid'],
                Response::HTTP_OK
            );
    }

    #[Route(
        "/api/user/change-data/{login}",
        methods: ['PATCH']
    )]
    #[Tag('User')]
    public function updateUserInfo(Request $request, $login): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data){
            return new JsonResponse(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->userInfoService->updateUserInfo($login, $data);

        return new JsonResponse(
            [
                'result' => 'Successfully updated'
            ], Response::HTTP_OK
        );
    }
}
