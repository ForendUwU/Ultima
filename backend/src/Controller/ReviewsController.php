<?php

namespace App\Controller;

use App\Service\ReviewsService;
use App\Service\TokenService;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class ReviewsController extends AbstractController
{
    public function __construct(
        private readonly ReviewsService $reviewsService,
        private readonly TokenService $tokenService
    ) {

    }

    #[Route(
        "/api/reviews/{gameId}",
        methods: ['POST']
    )]
    #[Tag('Review')]
    public function createGameReview(Request $request, $gameId): ?JsonResponse
    {
        $token = $request->headers->get('Authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['content']){
            return new JsonResponse(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->reviewsService->createGameReview($data['content'], $decodedToken->login, $gameId);
        } catch (\Exception $exception) {
            return new JsonResponse(
                $exception->getMessage(),
                $exception->getCode()
            );
        }

        return new JsonResponse(
            [
                'result' => 'Review was created successfully'
            ],
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/reviews/{gameId}",
        methods: ['GET']
    )]
    #[Tag('Review')]
    public function getGameReviews(Request $request, $gameId): ?JsonResponse
    {
        $result = $this->reviewsService->getGameReviews($gameId);

        return new JsonResponse(
            $result,
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/reviews/{gameId}",
        methods: ['PATCH']
    )]
    #[Tag('Review')]
    public function changeGameReviewContent(Request $request, $gameId): ?JsonResponse
    {
        $token = $request->headers->get('Authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['content']){
            return new JsonResponse(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->reviewsService->changeGameReviewContent($data['content'], $decodedToken->login, $gameId);

        return new JsonResponse(
            [
                'result' => 'Review was successfully changed'
            ],
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/reviews/{gameId}",
        methods: ['DELETE']
    )]
    #[Tag('Review')]
    public function deleteUsersReview(Request $request, $gameId): ?JsonResponse
    {
        $token = $request->headers->get('Authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        $this->reviewsService->deleteUsersReviewContent($decodedToken->login, $gameId);

        return new JsonResponse(
            [
                'result' => 'Review was successfully deleted'
            ],
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/user/review/{gameId}",
        methods: ['GET']
    )]
    #[Tag('User')]
    public function getUserReviewByGameId(Request $request, $gameId): JsonResponse
    {
        $token = $request->headers->get('Authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        try {
            $content = $this->reviewsService->getUserReviewContentByUserLoginAndGameId($decodedToken->login, $gameId);
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'result' => $exception->getMessage()
                ],
                $exception->getCode()
            );
        }

        return $content ?
            new JsonResponse(['result' => $content], Response::HTTP_OK) :
            new JsonResponse(['result' => ''], Response::HTTP_OK);
    }
}