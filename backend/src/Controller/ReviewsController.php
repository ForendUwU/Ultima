<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
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
        private ReviewRepository $reviewRepository,
        private readonly TokenService $tokenService
    ) {

    }

    #[Route(
        '/api/games/{gameId}/review',
        methods: ['POST']
    )]
    #[Tag('Review')]
    public function createGameReview(Request $request, $gameId): ?JsonResponse
    {
        $token = $request->headers->get('authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

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
            $this->reviewsService->createGameReview($data['content'], $decodedToken->id, $gameId);
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
        '/api/games/{gameId}/review',
        methods: ['PATCH']
    )]
    #[Tag('Review')]
    public function changeGameReviewContent(Request $request): ?JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['content'] || !$data['reviewId']){
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->reviewsService->changeGameReviewContent($data['content'], $data['reviewId']);
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
        '/api/games/{gameId}/review',
        methods: ['DELETE']
    )]
    #[Tag('Review')]
    public function deleteUsersReview(Request $request): ?JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['reviewId']){
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->reviewsService->deleteUsersReview($data['reviewId']);
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
        "/api/games/{gameId}/review",
        methods: ['GET']
    )]
    #[Tag('User')]
    public function getUserReview(Request $request, $gameId): JsonResponse
    {
        $token = $request->headers->get('authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        try {
            $result = $this->reviewsService->getUserReview($decodedToken->id, $gameId);
        } catch (\Exception $exception) {
            return $this->json(
                [
                    'message' => $exception->getMessage()
                ],
                $exception->getCode()
            );
        }

        return $result ?
            $this->json(
                [
                    'reviewId' => $result['id'],
                    'reviewContent' => $result['content'],
                    'rating' => $result['rating']
                ], Response::HTTP_OK) :
            $this->json(
                [
                    'reviewId' => '',
                    'reviewContent' => '',
                    'rating' => ''
                ], Response::HTTP_OK);
    }

    #[Route(
        "/api/rate",
        methods: ['POST']
    )]
    #[Tag('User')]
    public function rateGame(Request $request): JsonResponse
    {
        $token = $request->headers->get('authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['rating'] || !$data['gameId']){
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($data['reviewId']) {
            $review = $this->reviewRepository->findById($data['reviewId']);
        } else {
            $review = $this->reviewsService->createGameReview('', $decodedToken->id, $data['gameId']);
        }

        try {
            $this->reviewsService->sendRating($review->getId(), $data['rating']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }

        return $this->json([
            'message' => 'rated successfully'
        ], Response::HTTP_OK);
    }
}
