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
        private readonly ReviewsService $reviewsService
    ) {

    }

    #[Route(
        '/api/user/{userId}/games/{gameId}/review',
        methods: ['POST']
    )]
    #[Tag('Review')]
    public function createGameReview(Request $request, $userId, $gameId): ?JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['content']){
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->reviewsService->createGameReview($data['content'], $userId, $gameId);
        } catch (\Exception $exception) {
            return $this->json(
                [
                    'message' => $exception->getMessage()
                ],
                $exception->getCode()
            );
        }

        return $this->json(
            [
                'message' => 'Review was created successfully'
            ],
            Response::HTTP_OK
        );
    }

    #[Route(
        '/api/games/{gameId}/reviews',
        methods: ['GET']
    )]
    #[Tag('Review')]
    public function getGameReviews($gameId): ?JsonResponse
    {
        $result = $this->reviewsService->getGameReviews($gameId);

        return $this->json(
            $result,
            Response::HTTP_OK
        );
    }

    #[Route(
        '/api/user/{userId}/games/{gameId}/review',
        methods: ['PATCH']
    )]
    #[Tag('Review')]
    public function changeGameReviewContent(Request $request, $userId, $gameId): ?JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['content']){
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->reviewsService->changeGameReviewContent($data['content'], $userId, $gameId);
        } catch (\Exception $exception) {
            return $this->json(
                [
                    'message' => $exception->getMessage()
                ],
                $exception->getCode()
            );
        }

        return $this->json(
            [
                'message' => 'Review was successfully changed'
            ],
            Response::HTTP_OK
        );
    }

    #[Route(
        '/api/user/{userId}/games/{gameId}/review',
        methods: ['DELETE']
    )]
    #[Tag('Review')]
    public function deleteUsersReview(Request $request, $userId, $gameId): ?JsonResponse
    {
        try {
            $this->reviewsService->deleteUsersReview($userId, $gameId);
        } catch (\Exception $exception) {
            return $this->json(
                [
                    'message' => $exception->getMessage()
                ],
                $exception->getCode()
            );
        }

        return $this->json(
            [
                'message' => 'Review was successfully deleted'
            ],
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/user/{userId}/games/{gameId}/review",
        methods: ['GET']
    )]
    #[Tag('User')]
    public function getUserReviewContentByGameId(Request $request, $userId, $gameId): JsonResponse
    {
        try {
            $content = $this->reviewsService->getUserReviewContentByUserLoginAndGameId($userId, $gameId);
        } catch (\Exception $exception) {
            return $this->json(
                [
                    'message' => $exception->getMessage()
                ],
                $exception->getCode()
            );
        }

        return $content ?
            $this->json(['message' => $content], Response::HTTP_OK) :
            $this->json(['message' => ''], Response::HTTP_OK);
    }
}
